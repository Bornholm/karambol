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
namespace Karambol\Asset;

use Karambol\Provider\AppPathService;

/**
 * Gestion des assets
 * @package Karambol
 * @since 1.0.0
 * @license AGPLv3
 * @author William Petit
 */
class AssetService {
  
  /**
   * Contient les scripts
   * @var array
   */
  protected $scripts = [];
  
  /**
   * Contient les datas
   * @var array
   */
  protected $data = [];
  
  /**
   * Contient les feuilles de styles
   * @var array
   */
  protected $stylesheets = [];
  
  /**
   * Repertoire public
   * @var string
   */
  protected $publicDir;
  
  /**
   * Constructeur de classe
   * @param AppPathService $appPath
   */
  public function __construct(AppPathService $appPath) {
    $this->appPath = $appPath;
  }
  
  /**
   * Ajoute des scripts
   * @param array $scripts
   * @param array $data
   * @author William Petit
   */
  public function appendScripts(array $scripts, $data = []) {
    foreach($scripts as $sc) {
      if(!in_array($sc, $this->scripts)) $this->scripts[] = $sc;
    }
    $this->data = array_merge($this->data, $data);
  }
  
  /**
   * Renvoi les scripts
   * @return array Scripts
   * @author William Petit
   */
  public function getScripts() {
    return $this->scripts;
  }
  
  /**
   * Renvoi les Data
   * @return array Data
   * @author William Petit
   */
  public function getData() {
    return $this->data;
  }
  
  /**
   * Concatene les scripts
   * @return string
   * @author William Petit
   */
  public function packScripts() {

    $appPath = $this->appPath;
    $cachePrefix = 'cache/js';
    $cacheDir = $appPath->getPublicDir($cachePrefix);

    if(!is_dir($cacheDir)) mkdir($cacheDir, 0774, true);

    $scripts = $this->getScripts();
    $hash = sha1(implode('', $scripts));
    $cachedFilename = $hash.'.js';
    $localPath = $cachePrefix.DIRECTORY_SEPARATOR.$cachedFilename;
    $fullPath = $cacheDir.DIRECTORY_SEPARATOR.$cachedFilename;

    if(is_file($fullPath)) return $localPath;

    foreach($scripts as $sc) {
      $scriptContent = file_get_contents($appPath->getPublicDir($sc));
      file_put_contents($fullPath, $scriptContent.';', FILE_APPEND);
    }

    return $localPath;

  }

}
