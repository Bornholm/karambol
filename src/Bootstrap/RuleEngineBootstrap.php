<?php

namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Karambol\Provider;
use Karambol\RuleEngine;
use Karambol\RuleEngine\Rule;
use Karambol\RuleEngine\RuleEngineEvent;
use Karambol\RuleEngine\RuleEngineService;
use Karambol\RuleEngine\CustomizationListener;
use Karambol\RuleEngine\ExpressionFunctionProvider;
use Symfony\Component\HttpFoundation\Request;
use Silex\Application;
use Karambol\Entity\User;
use Karambol\Entity\RuleSet;
use Karambol\RuleEngine\DefaultCustomizationAPIListener;
use Karambol\RuleEngine\DefaultAccessControlAPIListener;
use Karambol\RuleEngine\DefaultCustomizationRulesListener;


class RuleEngineBootstrap implements BootstrapInterface {

  public function bootstrap(KarambolApp $app) {

    // Register rule engine service
    $app->register(new Provider\RuleEngineServiceProvider());

    $ruleEngine = $app['rule_engine'];

    // Add default api listeners
    $customizationAPIListener = new DefaultCustomizationAPIListener($app);
    $ruleEngine->addListener(RuleEngineEvent::BEFORE_EXECUTE_RULES, [$customizationAPIListener, 'onBeforeExecuteRules']);

    $accessControlAPIListener = new DefaultAccessControlAPIListener($app);
    $ruleEngine->addListener(RuleEngineEvent::BEFORE_EXECUTE_RULES, [$accessControlAPIListener, 'onBeforeExecuteRules']);

    // Add default rules listeners
    $customizationRulesListener = new DefaultCustomizationRulesListener();
    $ruleEngine->addListener(RuleEngineEvent::BEFORE_EXECUTE_RULES, [$customizationRulesListener, 'onBeforeExecuteRules']);

    // Execute customization rules on request
    $app->before([$this, 'onBeforeRequest']);

  }

  public function onBeforeRequest(Request $request, Application $app) {

    $logger = $app['monolog'];
    $ruleEngine = $app['rule_engine'];
    $rulesetRepo = $app['orm']->getRepository('Karambol\Entity\RuleSet');

    $ruleset = $rulesetRepo->findOneByName(RuleEngineService::CUSTOMIZATION);
    $rules = $ruleset->getRules()->toArray();

    $user = $app['user'] ? $app['user'] : new User();
    $vars = [
      'user' => $user->toAPIObject()
    ];

    try {
      $ruleEngine->execute(RuleEngineService::CUSTOMIZATION, $rules, $vars);
    } catch(\Exception $ex) {
      // TODO Store rule exception and provide the debugging information to the administrator
      $logger->error($ex);
    }

  }

}
