<?php

namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Karambol\Provider;
use Karambol\RuleEngine;
use Karambol\RuleEngine\Rule;
use Karambol\RuleEngine\RuleEngineEvent;
use Karambol\RuleEngine\CustomizationListener;
use Karambol\RuleEngine\ExpressionFunctionProvider;
use Symfony\Component\HttpFoundation\Request;
use Silex\Application;
use Karambol\Entity\RuleSet;
use Karambol\RuleEngine\DefaultCustomizationAPIListener;
use Karambol\RuleEngine\DefaultCustomizationRulesListener;


class RuleEngineBootstrap implements BootstrapInterface {

  public function bootstrap(KarambolApp $app) {

    // Register rule engine service
    $app->register(new Provider\RuleEngineServiceProvider());


    $ruleEngine = $app['rule_engine'];

    // Add default customization api listener
    $customizationAPIListener = new DefaultCustomizationAPIListener($app);
    $ruleEngine->addListener(RuleEngineEvent::BEFORE_EXECUTE_RULES, [$customizationAPIListener, 'onBeforeExecuteRules']);

    // Add default customization rules listener
    $customizationRulesListener = new DefaultCustomizationRulesListener();
    $ruleEngine->addListener(RuleEngineEvent::BEFORE_EXECUTE_RULES, [$customizationRulesListener, 'onBeforeExecuteRules']);

    // Execute customization rules on request
    $app->before([$this, 'onBeforeRequest']);

  }

  public function onBeforeRequest(Request $request, Application $app) {

    $logger = $app['monolog'];
    $ruleEngine = $app['rule_engine'];
    $rulesetRepo = $app['orm']->getRepository('Karambol\Entity\RuleSet');

    $ruleset = $rulesetRepo->findOneByName($ruleEngine::CUSTOMIZATION);
    $rules = $ruleset->getRules()->toArray();

    $vars = [
      'user' => $app['user']
    ];

    try {
      $ruleEngine->execute($ruleEngine::CUSTOMIZATION, $rules, $vars);
    } catch(\Exception $ex) {
      $logger->error($ex);
    }

  }

  public function getBaseRules(Application $app, $rulesetName) {
    $config = $app['config'];
    $rules = [];
    if( isset($config['base_rules'][$rulesetName]) ) {
      $configBaseRules = $config['base_rules'][$rulesetName];
      foreach($configBaseRules as $ruleData) {
        $rules[] = new Rule($ruleData['condition'], $ruleData['action']);
      }
    }
    return $rules;
  }

}
