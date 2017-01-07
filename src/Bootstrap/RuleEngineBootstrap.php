<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace Karambol\Bootstrap;

use Karambol\KarambolApp;
use Karambol\Provider;
use Karambol\RuleEngine\RuleEngineEvent;
use Karambol\RuleEngine\RuleEngine;
use Symfony\Component\HttpFoundation\Request;
use Silex\Application;
use Karambol\Entity\User;
use Karambol\RuleEngine\Listener\BaseCustomizationAPIListener;
use Karambol\RuleEngine\Listener\BaseAccessControlAPIListener;
use Karambol\RuleEngine\Context\Context;
use Karambol\AccessControl\Resource;
use Karambol\AccessControl\BaseActions;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Initialisation moteur de regle
 * @package Karambol
 * @license AGPLv3
 * @author William Petit
 */
class RuleEngineBootstrap implements BootstrapInterface {
  
  /**
   * Initialisation
   * @param KarambolApp $app
   * @author William Petit
   */
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
  
  /**
   * Customisation des règles
   * @param Request $request
   * @param Application $app
   * @author William Petit
   */
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
