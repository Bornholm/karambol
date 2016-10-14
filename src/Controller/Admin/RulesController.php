<?php

namespace Karambol\Controller\Admin;

use Karambol\KarambolApp;
use Karambol\Controller\Controller;
use Karambol\Entity\Page;
use Karambol\Entity\Ruleset;
use Karambol\Form\Type\RulesetType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Doctrine\Common\Collections\ArrayCollection;

class RulesController extends Controller {

  protected $rulesetName;

  public function __construct($rulesetName) {
    $this->rulesetName = $rulesetName;
  }

  public function mount(KarambolApp $app) {
    $rulesetName = $this->rulesetName;
    $app->get(sprintf('/admin/rules/%s', $rulesetName), [$this, 'showRules'])
      ->bind(sprintf('admin_rules_%s', $rulesetName))
    ;
    $app->post(sprintf('/admin/rules/%s', $rulesetName), [$this, 'handleRulesetUpsert'])
      ->bind(sprintf('admin_ruleset_upsert_%s', $rulesetName))
    ;
  }

  public function showRules() {

    $twig = $this->get('twig');
    $orm = $this->get('orm');

    $ruleset = $orm->getRepository('Karambol\Entity\Ruleset')
      ->findOneByName($this->rulesetName)
    ;

    if($ruleset === null) {
      $ruleset = new Ruleset();
      $ruleset->setName($this->rulesetName);
    }

    $rulesetForm = $this->getRulesetForm($ruleset);

    return $twig->render('admin/rules/index.html.twig', [
      'rulesetForm' => $rulesetForm->createView(),
      'ruleset' => $ruleset
    ]);

  }

  public function handleRulesetUpsert() {

    $twig = $this->get('twig');
    $orm = $this->get('orm');
    $request = $this->get('request');

    $ruleset = $orm->getRepository('Karambol\Entity\Ruleset')
      ->findOneByName($this->rulesetName)
    ;

    if($ruleset === null) {
      $ruleset = new Ruleset();
      $ruleset->setName($this->rulesetName);
    }

    $rulesetForm = $this->getRulesetForm($ruleset);

    $rulesetForm->handleRequest($request);

    if( !$rulesetForm->isValid() ) {
      return $twig->render('admin/rules/index.html.twig', [
        'rulesetForm' => $rulesetForm->createView(),
        'ruleset' => $ruleset
      ]);
    }

    $ruleset = $rulesetForm->getData();

    if( $ruleset->getId() === null ) {
      $orm->persist($ruleset);
    }

    $orm->flush();

    $urlGen = $this->get('url_generator');
    return $this->redirect($urlGen->generate(sprintf('admin_rules_%s', $this->rulesetName)));

  }

  public function getRulesetForm(Ruleset $ruleset) {

    $formFactory = $this->get('form.factory');
    $urlGen = $this->get('url_generator');

    $formBuilder = $formFactory->createBuilder(RulesetType::class, $ruleset);
    $action = $urlGen->generate(sprintf('admin_ruleset_upsert_%s', $ruleset->getName()));

    return $formBuilder->setAction($action)
      ->setMethod('POST')
      ->getForm()
    ;

  }

}
