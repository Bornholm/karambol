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
namespace Karambol\Controller;

use Karambol\KarambolApp;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

/**
 * Interface controller
 * @package Karambol
 * @license AGPLv3
 * @author William Petit
 */
class DocumentationController extends Controller {
  
  /**
   * Definition des routes
   * @param KarambolApp $app Application
   * @author William Petit
   */
  public function mount(KarambolApp $app) {
    $app->get('/doc/{docFile}', [$this, 'showDocumentation'])
      ->assert('docFile', '.+')
      ->bind('documentation')
    ;
  }
  
  /**
   * Renvoi le fichier de documentation
   * @param string $docFile
   * @return File
   * @throws ResourceNotFoundException
   */
  public function showDocumentation($docFile) {

    $twig = $this->get('twig');
    $appPath = $this->get('app_path');

    $docPathPrefix = 'doc'.DIRECTORY_SEPARATOR;
    $filePath = $docPathPrefix.$docFile;

    if(!$appPath->existsInApp($filePath)) throw new ResourceNotFoundException();

    $isMarkdown = preg_match('/\.(md|markdown|commonmark)$/', $docFile);

    if($isMarkdown) {
      return $twig->render('documentation/index.html.twig', [
        'docFile' => $docFile
      ]);
    }

    return $this->app->sendFile($appPath->getPath($filePath));

  }

}
