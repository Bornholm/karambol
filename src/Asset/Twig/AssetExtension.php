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
namespace Karambol\Asset\Twig;

use \Twig_Extension;
use \Twig_SimpleFunction;
use \Twig_Environment;

/**
 * Gestion des assets
 * @package Karambol
 * @since 1.0.0
 * @license AGPLv3
 * @author William Petit
 */
class AssetExtension extends Twig_Extension {

  use \Karambol\Util\AppAwareTrait;
  
  /**
   * Renvoi les methodes twig
   * @return array
   * @author William Petit
   */
  public function getFunctions() {
    return [
      new Twig_SimpleFunction('asset', [$this, 'getAssetUrl'], ['is_safe' => ['html']]),
      new Twig_SimpleFunction('appendScript', [$this, 'appendScript'], ['is_safe' => ['html', 'js']]),
      new Twig_SimpleFunction('renderScripts', [$this, 'renderScripts'], ['is_safe' => ['html', 'js']])
    ];
  }
  
  /**
   * Renvoi le nom
   * @return string
   * @author William Petit
   */
  public function getName() {
    return 'karambol_asset_extension';
  }
  
  /**
   * Renvoi l' URL des assets
   * @return string
   * @author William Petit
   */
  public function getAssetUrl($assetPublicPath) {
    return $this->pathToUrl($assetPublicPath);
  }
  
  /**
   * Ajoute un script
   * @param string $scriptPublicPath
   * @param array $data
   * @author William Petit
   */
  public function appendScript($scriptPublicPath, $data = []) {
    $scriptPublicPath = !is_array($scriptPublicPath) ? [$scriptPublicPath] : $scriptPublicPath;
    $this->app['assets']->appendScripts($scriptPublicPath, $data);
  }
  
  /**
   * Renvoi le tag d'insertion d'un script
   * @return string
   * @author William Petit
   */
  public function renderScripts() {

    $debug = $this->app['debug'];
    $assetsSvc = $this->app['assets'];

    $scriptTag = '<script src="%s" %s></script>';
    $tags = '';

    $dataAttributes = $this->dataToAttributes($assetsSvc->getData());

    if($debug) {
      foreach($assetsSvc->getScripts() as $script) {
        $tags .= sprintf($scriptTag, $this->pathToUrl($script), $dataAttributes);
      }
    } else {
      $cachedScript = $assetsSvc->packScripts();
      $tags = sprintf($scriptTag, $this->pathToUrl($cachedScript), $dataAttributes);
    }

    return $tags;

  }
  
  /**
   * Transforme un chemin en URL
   * @param string $assetPublicPath
   * @return string
   * @author William Petit
   */
  protected function pathToUrl($assetPublicPath) {
    $req = $this->app['request'];
    $baseUrl = $req->getBasePath();
    return sprintf($baseUrl.'/%s', ltrim($assetPublicPath, '/'));
  }
  
  /**
   * Transforme tableau de data en attribut html (data-{attr})
   * @param Array $data
   * @return string
   * @author William Petit
   */
  protected function dataToAttributes(array $data) {
    $attrs = '';
    foreach($data as $key => $value) {
      $serializedValue = is_array($value) ? json_encode($value) : $value;
      $attrs .= sprintf('data-%s="%s"', $key, htmlspecialchars($serializedValue, ENT_QUOTES, 'UTF-8'));
    }
    return $attrs;
  }

}
