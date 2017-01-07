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
namespace Karambol;

use Silex\Application;

/**
 * Classe principale
 * @package Karambol
 * @since 1.0.0
 * @license AGPLv3
 */
class KarambolApp extends Application {

  /**
   * Constructeur de class
   * @since 1.0.0
   * @author William Petit
   */
  public function __construct() {
    parent::__construct();
    $this->bootstrapApplication();
  }

  /**
   * Instancie l'ensemble des éléments nécessaire à l'application
   * @since 1.0.0
   * @author William Petit
   */
  protected function bootstrapApplication() {

    $bootrappers = [
        'Karambol\Bootstrap\ConfigBootstrap',
        'Karambol\Bootstrap\AppPathBootstrap',
        'Karambol\Bootstrap\SecurityBootstrap',
        'Karambol\Bootstrap\TwigBootstrap',
        'Karambol\Bootstrap\DebugBarBootstrap',
        'Karambol\Bootstrap\DoctrineBootstrap',
        'Karambol\Bootstrap\SettingBootstrap',
        'Karambol\Bootstrap\MailerBootstrap',
        'Karambol\Bootstrap\AccountBootstrap',
        'Karambol\Bootstrap\MonologBootstrap',
        'Karambol\Bootstrap\RuleEngineBootstrap',
        'Karambol\Bootstrap\SessionBootstrap',
        'Karambol\Bootstrap\FormBootstrap',
        'Karambol\Bootstrap\UrlGeneratorBootstrap',
        'Karambol\Bootstrap\AssetBootstrap',
        'Karambol\Bootstrap\SlugifyBootstrap',
        'Karambol\Bootstrap\PageBootstrap',
        'Karambol\Bootstrap\ThemeBootstrap',
        'Karambol\Bootstrap\MenuBootstrap',
        'Karambol\Bootstrap\LocalizationBootstrap',
        'Karambol\Bootstrap\ControllersBootstrap',
        'Karambol\Bootstrap\ConsoleBootstrap',
        'Karambol\Bootstrap\PluginsBootstrap'
    ];

    foreach ($bootrappers as $bootstrapClass) {
      $bootstrapper = new $bootstrapClass();
      $bootstrapper->bootstrap($this);
    }
  }

}
