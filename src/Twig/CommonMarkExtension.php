<?php

namespace Karambol\Twig;

use \Twig_Extension;
use \Twig_SimpleFunction;
use \Twig_Environment;
use League\CommonMark\Environment;
use League\CommonMark\HtmlElement;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Inline\Element\Link;
use League\CommonMark\Inline\Element\AbstractInline;
use League\CommonMark\Block\Renderer\BlockRendererInterface;
use League\CommonMark\Block\Element\Heading;
use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Inline\Renderer\InlineRendererInterface;
use League\CommonMark\ElementRendererInterface;
use Cocur\Slugify\Slugify;

class CommonMarkExtension extends Twig_Extension {

  use \Karambol\Util\AppAwareTrait;

  public function getFunctions() {
    return [
      new Twig_SimpleFunction('markdown', [$this, 'handleMarkdownFunction'], ['is_safe' => ['html']]),
      new Twig_SimpleFunction('include_markdown_file', [$this, 'handleIncludeMarkdownFileFunction'], ['is_safe' => ['html']])
    ];
  }

  public function getName() {
    return 'karambol_commonmark_extension';
  }

  public function handleMarkdownFunction($markdown, $options = []) {
    return $this->markdownToHTML($markdown, $options);
  }

  public function handleIncludeMarkdownFileFunction($markdownFile, $options = []) {
    $filePath = $this->app['app_path']->getPath($markdownFile);
    if(!is_file($filePath)) return '';
    return $this->markdownToHTML(file_get_contents($filePath), $options);
  }

  protected function markdownToHTML($markdown, $options = []) {

    $environment = Environment::createCommonMarkEnvironment();

    if(isset($options['rewrite_markdown_links'])) {
      $environment->addInlineRenderer(Link::class, new MarkdownLinkRenderer(urldecode($options['rewrite_markdown_links'])));
    }

    if(isset($options['shift_titles']) && is_integer($options['shift_titles'])) {
      $environment->addBlockRenderer(Heading::class, new HeadingShifterRenderer($options['shift_titles']));
    }

    $converter = new CommonMarkConverter([], $environment);

    return $converter->convertToHtml($markdown);
  }

}

class MarkdownLinkRenderer implements InlineRendererInterface {

  protected $urlPattern;

  public function __construct($urlPattern) {
    $this->urlPattern = $urlPattern;
  }

  public function render(AbstractInline $inline, ElementRendererInterface $htmlRenderer, $inThightList = false) {
    $attrs = [];
    $url = $htmlRenderer->escape($inline->getUrl(), true);
    $isMarkdownLink = preg_match('/\.(md|markdown|commonmark)/i', $url);
    if($isMarkdownLink) $url = sprintf($this->urlPattern, $url);
    $attrs['href'] = $url;
    return new HtmlElement('a', $attrs, $htmlRenderer->renderInlines($inline->children()));
  }

}

class HeadingShifterRenderer implements BlockRendererInterface {

  protected $shift;

  public function __construct($shift = 0) {
    $this->shift = $shift;
  }

  public function render(AbstractBlock $block, ElementRendererInterface $htmlRenderer, $inThightList = false) {
    if (!($block instanceof Heading)) {
      throw new \InvalidArgumentException('Incompatible block type: ' . get_class($block));
    }
    $level = $block->getLevel() + $this->shift;
    $tag = 'h' . ($level >= 1 ? $level : 1);
    $attrs = [];
    foreach ($block->getData('attributes', []) as $key => $value) {
      $attrs[$key] = $htmlRenderer->escape($value, true);
    }

    if(!isset($attrs['id'])) {
      $slugify = new Slugify();
      $attrs['id'] = $slugify->slugify($block->getStringContent());
    }

    return new HtmlElement($tag, $attrs, $htmlRenderer->renderInlines($block->children()));
  }

}
