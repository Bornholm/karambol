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

/**
 * Authentification
 * @package Karambol
 * @license AGPLv3
 * @author William Petit
 */
class AuthenticationController extends Controller {
  
  /**
   * Definition des routes
   * @param KarambolApp $app Application
   * @author William Petit
   */
  public function mount(KarambolApp $app) {
    $app->get('/login', [$this, 'showLogin'])->bind('login');
  }
  
  /**
   * Page de login
   * @return View
   */
  public function showLogin() {
    $lastError = $this->get('security.last_error');
    $lastError = $lastError($this->get('request'));
    if($lastError) $this->addFlashMessage('auth.'.$lastError, ['type' => 'error']);
    return $this->render('auth/login.html.twig', [
      'lastUsername' => $this->get('session')->get('_security.last_username')
    ]);
  }

}
