<?php

namespace Karambol\Controller;

use Karambol\KarambolApp;
use Karambol\Page\PageInterface;
use Karambol\AccessControl\BaseActions;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class DocumentationController extends Controller {

  public function mount(KarambolApp $app) {
    $app->get('/doc/{docFile}', [$this, 'showDocumentation'])
      ->assert('docFile', '.+')
      ->bind('documentation')
    ;
  }

  public function showDocumentation($docFile) {

    $twig = $this->get('twig');
    $appPath = $this->get('app_path');

    $docPathPrefix = 'doc'.DIRECTORY_SEPARATOR;
    $filePath = $docPathPrefix.$docFile;

    if(!$appPath->existsInApp($filePath)) throw new ResourceNotFoundException();

    $isMarkdown = preg_match('/\.(md|markdown|commonmark)$/', $docFile);

    if($isMarkdown) {
      return $twig->render('documentation/index.html.twig', [
        'docFile' => $docFile
      ]);
    }

    return $this->app->sendFile($appPath->getPath($filePath));

  }

}
