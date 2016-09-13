<?php

namespace Karambol\Controller;

use Karambol\KarambolApp;
use Karambol\Account\Exception\AccountExistsException;
use Karambol\Account\Exception\EmailExistsException;
use Karambol\Entity\User;
use Karambol\Form\Type\RegisterType;
use Symfony\Component\Form\FormError;

class RegistrationController extends Controller {

  public function mount(KarambolApp $app) {
    $app->get('/register', [$this, 'showRegister'])->bind('register');
    $app->post('/register', [$this, 'handleRegisterForm'])->bind('handle_register');
  }

  public function showRegister() {
    $form = $this->getRegisterForm();
    return $this->render('registration/register.html.twig', [
      'registerForm' => $form->createView()
    ]);
  }

  public function handleRegisterForm() {

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
