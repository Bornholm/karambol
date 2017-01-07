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
use Karambol\Provider\PageServiceProvider;
use Karambol\Page\BasePagesSubscriber;

/**
 * Initialisation page
 * @package Karambol
 * @license AGPLv3
 * @author William Petit
 */
class PageBootstrap implements BootstrapInterface {
  
   /**
   * Initialisation
   * @param KarambolApp $app
   * @author William Petit
   */
  public function bootstrap(KarambolApp $app) {
    $app->register(new PageServiceProvider());
    $app['pages']->addSubscriber(new BasePagesSubscriber($app));
  }

}
