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
use Karambol\Account\Exception\AccountExistsException;
use Karambol\Account\Exception\EmailExistsException;
use Karambol\Form\Type\RegisterType;
use Symfony\Component\Form\FormError;

/**
 * Enregistrement controller
 * @package Karambol
 * @license AGPLv3
 * @author William Petit
 */
class RegistrationController extends Controller {
  
  /**
   * Definition des routes
   * @param KarambolApp $app Application
   * @author William Petit
   */
  public function mount(KarambolApp $app) {
    $app->get('/register', [$this, 'showRegister'])->bind('register');
    $app->post('/register', [$this, 'handleRegisterForm'])->bind('handle_register');
  }
  
  /**
   * Page enregistrement
   * @return View
   * @author William Petit
   */
  public function showRegister() {
    $form = $this->getRegisterForm();
    return $this->render('registration/register.html.twig', [
      'registerForm' => $form->createView()
    ]);
  }
  
  /**
   * Traitement de l'enregistrement
   * @return redirect
   * @author William Petit
   */
  public function handleRegisterForm() {

    $settings = $this->get('settings');
    if(!$settings->get('allow_registration')) return $this->abort(403);

    $request = $this->get('request');

    $form = $this->getRegisterForm();

    $form->handleRequest($request);

    if(!$form->isValid()) {
      return $this->render('registration/register.html.twig', [
        'registerForm' => $form->createView()
      ]);
    }

    $accounts = $this->get('accounts');
    $orm = $this->get('orm');
    $data = $form->getData();

    $email = $data['email'];

    $tempPassword = substr(base64_encode(random_bytes(18)), 0, -2);

    // Try to create account
    try {
      $user = $accounts->createAccount($data['username'], $tempPassword, $data['email']);
    } catch(AccountExistsException $ex) {
      $translator = $this->get('translator');
      $form->get('username')->addError(new FormError($translator->trans('registration.username_already_exists')));
      return $this->render('registration/register.html.twig', [
        'registerForm' => $form->createView()
      ]);
    } catch(EmailExistsException $ex) {
      $translator = $this->get('translator');
      $form->get('email')->get('first')->addError(new FormError($translator->trans('registration.email_already_exists')));
      return $this->render('registration/register.html.twig', [
        'registerForm' => $form->createView()
      ]);
    }

    $accounts->sendPasswordResetEmail($user);

    $this->addFlashMessage('registration.successfully_registered', ['type' => 'success']);

    return $this->redirect($this->get('url_generator')->generate('home'));

  }
  
  /**
   * Renvoi le formulaire d'inscription
   * @return Form
   * @author William Petit
   */
  protected function getRegisterForm() {

    $formFactory = $this->get('form.factory');
    $urlGen = $this->get('url_generator');
    $translator = $this->get('translator');

    $formBuilder = $formFactory->createBuilder(RegisterType::class, [], [
      'translator' => $translator
    ]);
    $action = $urlGen->generate('handle_register');

    return $formBuilder->setAction($action)
      ->setMethod('POST')
      ->getForm()
    ;

  }

}
