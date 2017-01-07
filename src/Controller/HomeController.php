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
use Karambol\Page\PageInterface;
use Karambol\AccessControl\BaseActions;

/**
 * Home controller
 * @package Karambol
 * @license AGPLv3
 * @author William Petit
 */
class HomeController extends Controller {

  /**
   * Definition des routes
   * @param KarambolApp $app Application
   * @author William Petit
   */
  public function mount(KarambolApp $app) {
    $app->get('/', [$this, 'showHome'])->bind('home');
    $app->get('/home', [$this, 'showDefaultHome'])->bind('default_home');
    $app->get('/p/{pageSlug}', [$this, 'showFramedPage'])->bind('framed_page');
  }
  
  /**
   * Page d'accueil
   * @return page d'accueil par defaut
   * @author William Petit
   */
  public function showHome() {

    $homePage = $this->get('pages')->getHomepage();
    if($homePage instanceof PageInterface) {
      return $this->redirect($homePage->getUrl());
    }
    return $this->showDefaultHome();
  }
  
  /**
   * Page par defaut
   * @return View
   * @author William Petit
   */
  public function showDefaultHome() {
    $twig = $this->get('twig');
    return $twig->render('home/index.html.twig');
  }
  
  /**
   * Page dans frame
   * @param type $pageSlug
   * @return View
   * @author William Petit
   */
  public function showFramedPage($pageSlug) {

    $twig = $this->get('twig');
    $pagesSvc = $this->get('pages');

    $page = $pagesSvc->findOne(['slug' => $pageSlug]);

    if(!$page) return $this->app->abort(404, 'Page not found !');

    $authChecker = $this->get('security.authorization_checker');
    $authChecker->isGranted(BaseActions::READ, $page);

    return $twig->render('home/page.html.twig', [
      'page' => $page
    ]);
  }

}
