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
use Karambol\Form\Type\ProfileType;
use Karambol\Entity\User;

/**
 * Profile controller
 * @package Karambol
 * @license AGPLv3
 * @author William Petit
 */
class ProfileController extends Controller {

  /**
   * Definition des routes
   * @param KarambolApp $app Application
   * @author William Petit
   */
  public function mount(KarambolApp $app) {
    $app->get('/profile', [$this, 'showProfile'])->bind('profile');
    $app->post('/profile', [$this, 'handleProfileForm'])->bind('handle_profile');
  }
  
  /**
   * Affichage du profil
   * @return View
   * @author William Petit
   */
  public function showProfile() {

    $twig = $this->get('twig');
    $user = $this->get('user');

    $form = $this->getProfileForm($user);

    return $twig->render('user/profile.html.twig', [
      'profileForm' => $form->createView(),
      'user' => $user
    ]);

  }
  
  /**
   * Mise a jour du profil
   * @return redirect
   * @author William Petit
   */
  public function handleProfileForm() {

    $twig = $this->get('twig');
    $request = $this->get('request');
    $user = $this->get('user');

    $form = $this->getProfileForm($user);

    $form->handleRequest($request);

    if( !$form->isValid() ) {
      return $twig->render('user/profile.html.twig', [
        'profileForm' => $form->createView(),
        'user' => $user
      ]);
    }

    $password = $form->get('password')->getData();
    if(!empty($password)) {
      $accounts = $this->get('accounts');
      $accounts->changePassword($user, $password);
    }

    $orm = $this->get('orm');
    $orm->flush();

    $urlGen = $this->get('url_generator');
    return $this->redirect($urlGen->generate('profile'));

  }
  
  /**
   * Renvoie le formulaire mise a jour profil
   * @param User $user
   * @return Form
   */
  protected function getProfileForm(User $user) {

    $formFactory = $this->get('form.factory');
    $urlGen = $this->get('url_generator');

    $formBuilder = $formFactory->createBuilder(ProfileType::class, $user);
    $action = $urlGen->generate('handle_profile');

    return $formBuilder->setAction($action)
      ->setMethod('POST')
      ->getForm()
    ;

  }

}
