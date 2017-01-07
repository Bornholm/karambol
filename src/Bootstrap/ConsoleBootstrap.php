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
use Karambol\Provider\ConsoleServiceProvider;
use Karambol\Command;

/**
 * Initialisation console
 * @package Karambol
 * @license AGPLv3
 * @author William Petit
 */
class ConsoleBootstrap implements BootstrapInterface {
  
  /**
   * Initialisation
   * @param KarambolApp $app
   * @author William Petit
   */
  public function bootstrap(KarambolApp $app) {

    $app->register(new ConsoleServiceProvider());

    // Register default commands
    $console = $app['console'];

    $console->add(new Command\LinkAssetsCommand($app));
    $console->add(new Command\CreateAccountCommand($app));
    $console->add(new Command\PromoteAccountCommand($app));
    $console->add(new Command\PluginConfigCommand($app));
    $console->add(new Command\DumpRulesCommand($app));
    $console->add(new Command\LoadRulesCommand($app));
    $console->add(new Command\ClearCacheCommand($app));
    $console->add(new Command\CreatePluginCommand($app));

  }

}
