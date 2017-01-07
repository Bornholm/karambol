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
use Silex\Provider\SecurityServiceProvider;
use Karambol\Security\UserProvider;
use Karambol\AccessControl\Voter\RuleEngineAccessControlVoter;

/**
 * Initialisation securite
 * @package Karambol
 * @license AGPLv3
 * @author William Petit
 */
class SecurityBootstrap implements BootstrapInterface {
  
  /**
   * Application
   * @var KarambolApp
   */
  protected $app;
  
  /**
   * Initialisation
   * @param KarambolApp $app
   * @author William Petit
   */
  public function bootstrap(KarambolApp $app) {

    $this->app = $app;
    $that = $this;

    $app->register(new SecurityServiceProvider(), [
      'security.firewalls' => [
        'main' => [
          'pattern' => '^.*$',
          'anonymous' => true,
          'users' => function() use ($app, $that) {
            $provider = new UserProvider($app);
            return $provider;
          },
          'form' => [
            'login_path' => '/login',
            'check_path' => '/login_check'
          ],
          'logout' => array('logout_path' => '/logout', 'invalidate_session' => true)
        ]
      ]
    ]);

    $app['security.voters'] = $app->share($app->extend('security.voters', function ($voters, $app) {
      $voters[] = new RuleEngineAccessControlVoter($app);
      return $voters;
    }));


  }

}
