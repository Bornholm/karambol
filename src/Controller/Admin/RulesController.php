<?php

namespace Karambol\Controller\Admin;

use Karambol\KarambolApp;
use Karambol\Controller\Controller;
use Karambol\Form\Type\PersistentRuleSetType;
use Karambol\Entity\PersistentRuleSet;

class RulesController extends Controller {

  public function mount(KarambolApp $app) {
    $app->get('/admin/rules', array($this, 'showRulesIndex'))->bind('admin_rules');
  }

  public function showRulesIndex() {
    $twig = $this->get('twig');
    $form = $this->getRuleSetForm();
    return $twig->render('admin/rules/index.html.twig', [
      'form' => $form->createView()
    ]);
  }

  protected function getRuleSetForm(PersistentRuleSet $set = null) {

    $formFactory = $this->get('form.factory');
    $urlGen = $this->get('url_generator');

    if($set === null) $set = new PersistentRuleSet();

    $formBuilder = $formFactory->createBuilder(PersistentRuleSetType::class, $set);
    $action = $urlGen->generate('admin_rules');

    return $formBuilder->setAction($action)
      ->setMethod('POST')
      ->getForm()
    ;

  }

}
