<?php

namespace Karambol\Controller\Admin;

use Karambol\KarambolApp;
use Karambol\Controller\Controller;
use Karambol\Form\Type\RuleSetType;
use Karambol\Form\Model\RuleSetModel;
use Karambol\Entity\PersistentRuleSet;
use Karambol\Entity\PersistentRule;
use Karambol\RuleEngine\Rule\PropertyTestRule;

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

    $persistentRule = new PersistentRule();
    $rule = new PropertyTestRule();
    $persistentRule->setInternalRule($rule);
    $set->addRule($persistentRule);

    $model = RuleSetModel::fromPersistentRuleSet($set);

    dump($model);

    $formBuilder = $formFactory->createBuilder(RuleSetType::class, $model);
    $action = $urlGen->generate('admin_rules');

    return $formBuilder->setAction($action)
      ->setMethod('POST')
      ->getForm()
    ;

  }

}
