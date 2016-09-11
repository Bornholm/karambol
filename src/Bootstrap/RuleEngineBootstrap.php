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
use Karambol\Entity\BaseUser;
use Karambol\Entity\RuleSet;
use Karambol\RuleEngine\BaseCustomizationAPIListener;
use Karambol\RuleEngine\BaseAccessControlAPIListener;
use Karambol\RuleEngine\RuleEngineVariableViewInterface;
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

    $logger = $app['monolog'];
    $ruleEngine = $app['rule_engine'];
    $rulesetRepo = $app['orm']->getRepository('Karambol\Entity\RuleSet');

    $ruleset = $rulesetRepo->findOneByName(RuleEngine::CUSTOMIZATION);

    if(!$ruleset) return;

    $rules = $ruleset->getRules()->toArray();

    $user = $app['user'] ? $app['user'] : new BaseUser();
    $vars = [
      'user' => $user instanceof RuleEngineVariableViewInterface ? $user->createRuleEngineView() : $user
    ];

    try {
      $ruleEngine->execute(RuleEngine::CUSTOMIZATION, $rules, $vars);
    } catch(\Exception $ex) {
      // TODO Store rule exception and provide the debugging information to the administrator
      $logger->error($ex);
    }

  }

  public function URLAccessAuthorizationMiddleware(Request $request, Application $app) {

    $request = $app['request'];
    $resource = new Resource('url', $request->getRequestURI());

    $authCheck =  $app['security.authorization_checker'];
    $canAccess = $authCheck->isGranted(BaseActions::ACCESS, $resource);

    if(!$canAccess) throw new AccessDeniedException();

  }

}
