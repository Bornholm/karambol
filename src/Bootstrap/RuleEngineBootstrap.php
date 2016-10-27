<?php

namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Karambol\Provider;
use Karambol\RuleEngine\Rule;
use Karambol\RuleEngine\RuleEngineEvent;
use Karambol\RuleEngine\RuleEngine;
use Karambol\RuleEngine\CustomizationListener;
use Karambol\RuleEngine\ExpressionFunctionProvider;
use Symfony\Component\HttpFoundation\Request;
use Silex\Application;
use Karambol\Entity\User;
use Karambol\Entity\Ruleset;
use Karambol\RuleEngine\Listener\BaseCustomizationAPIListener;
use Karambol\RuleEngine\Listener\BaseAccessControlAPIListener;
use Karambol\RuleEngine\VariableViewInterface;
use Karambol\RuleEngine\Context\Context;
use Karambol\AccessControl\Resource;
use Karambol\AccessControl\BaseActions;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class RuleEngineBootstrap implements BootstrapInterface {

  public function bootstrap(KarambolApp $app) {

    // Register rule engine service
    $app->register(new Provider\RuleEngineServiceProvider());

    $ruleEngine = $app['rule_engine'];

    // Add default api listeners
    $customizationAPIListener = new BaseCustomizationAPIListener($app);
    $ruleEngine->addListener(RuleEngineEvent::BEFORE_EXECUTE_RULES, [$customizationAPIListener, 'onBeforeExecuteRules']);

    $accessControlAPIListener = new BaseAccessControlAPIListener($app);
    $ruleEngine->addListener(RuleEngineEvent::BEFORE_EXECUTE_RULES, [$accessControlAPIListener, 'onBeforeExecuteRules']);

    // Execute customization rules on request
    $app->before([$this, 'customizationRulesMiddleware']);
    // Check URL access authorization
    $app->before([$this, 'URLAccessAuthorizationMiddleware']);

  }

  public function customizationRulesMiddleware(Request $request, Application $app) {

    $logger = $app['logger'];
    $debugBar = $app['debug_bar'];
    $ruleEngine = $app['rule_engine'];
    $rulesetRepo = $app['orm']->getRepository('Karambol\Entity\Ruleset');

    $debugBar['time']->startMeasure('customization_rules', 'Customization rules execution');

    $ruleset = $rulesetRepo->findOneByName(RuleEngine::CUSTOMIZATION);

    if(!$ruleset) return;

    $rules = $ruleset->getRules()->toArray();

    $user = $app['user'] ? $app['user'] : new User();
    $context = new Context();
    $context->expose('user', $user);

    try {
      $ruleEngine->execute(RuleEngine::CUSTOMIZATION, $rules, $context);
    } catch(\Exception $ex) {
      // TODO Store rule exception and provide the debugging information to the administrator
      $logger->error($ex);
    }

    $debugBar['time']->stopMeasure('customization_rules');

  }

  public function URLAccessAuthorizationMiddleware(Request $request, Application $app) {

    $request = $app['request'];
    $resource = new Resource('url', $request->getRequestURI());

    $authCheck =  $app['security.authorization_checker'];
    $canAccess = $authCheck->isGranted(BaseActions::ACCESS, $resource);

    if(!$canAccess) throw new AccessDeniedException();

  }

}
