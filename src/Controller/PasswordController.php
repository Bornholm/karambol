<?php

namespace Karambol\Controller;

use Karambol\KarambolApp;
use Karambol\Form\Type\PasswordResetRequestType;
use Karambol\Form\Type\PasswordResetType;
use Karambol\Entity\User;

class PasswordController extends Controller {

  public function mount(KarambolApp $app) {

    $app->get('/password', [$this, 'showResetRequest'])->bind('password_reset_request');
    $app->post('/password', [$this, 'handleResetRequest'])->bind('handle_password_reset_request');

    $app->get('/password/{token}', [$this, 'showPasswordReset'])->bind('password_reset');
    $app->post('/password/{token}', [$this, 'handlePasswordReset'])->bind('handle_password_reset');

  }


  public function showResetRequest($token = null) {
    $form = $this->getResetRequestForm();
    return $this->render('password_reset/reset_request.html.twig', [
      'resetRequestForm' => $form->createView()
    ]);
  }

  public function handleResetRequest() {

    $request = $this->get('request');

    $form = $this->getResetRequestForm();

    $form->handleRequest($request);

    if(!$form->isValid()) {
      return $this->render('password/reset.html.twig', [
        'passwordResetForm' => $form->createView()
      ]);
    }

    $email = $form->get('email')->getData();
    $orm = $this->get('orm');

    $user = $orm->getRepository(User::class)->findOneByEmail($email);

    if(!$user) {
      $translator = $this->get('translator');
      $form->get('email')->addError(new FormError($translator->trans('password_reset.email_unknown')));
      return $this->render('password/reset.html.twig', [
        'passwordResetForm' => $form->createView()
      ]);
    }

    $this->get('accounts')->sendPasswordResetEmail($user);

    $this->addFlashMessage('password_reset.reset_email_sent', ['type' => 'success']);

    return $this->redirect($this->get('url_generator')->generate('home'));

  }

  public function showPasswordReset($token) {

    $accounts = $this->get('accounts');
    $user = $accounts->findUserForPasswordToken($token);

    if(!$user) {
      $this->addFlashMessage('password_reset.invalid_token', ['type' => 'error']);
      return $this->redirect($this->get('url_generator')->generate('home'), 302);
    }

    $form = $this->getResetForm($token);

    return $this->render('password_reset/reset.html.twig', [
      'resetForm' => $form->createView()
    ]);

  }

  public function handlePasswordReset($token) {

    $accounts = $this->get('accounts');
    $user = $accounts->findUserForPasswordToken($token);

    if(!$user) {
      $this->addFlashMessage('password_reset.invalid_token', ['type' => 'error']);
      return $this->redirect($this->get('url_generator')->generate('home'), 302);
    }

    $form = $this->getResetForm($token);

    $form->handleRequest($this->get('request'));

    if(!$form->isValid()) {
      return $this->render('password_reset/reset.html.twig', [
        'resetForm' => $form->createView()
      ]);
    }

    $newPassword = $form->get('password')->getData();

    $accounts->changePassword($user, $newPassword);

    $user->clearPasswordToken();
    $this->get('orm')->flush();

    $this->addFlashMessage('password_reset.new_password_saved', ['type' => 'success']);

    return $this->redirect($this->get('url_generator')->generate('login'), 302);

  }

  protected function getResetRequestForm() {

    $formFactory = $this->get('form.factory');
    $urlGen = $this->get('url_generator');

    $formBuilder = $formFactory->createBuilder(PasswordResetRequestType::class, []);
    $action = $urlGen->generate('handle_password_reset_request');

    return $formBuilder->setAction($action)
      ->setMethod('POST')
      ->getForm()
    ;

  }

  protected function getResetForm($token) {

    $formFactory = $this->get('form.factory');
    $urlGen = $this->get('url_generator');

    $formBuilder = $formFactory->createBuilder(PasswordResetType::class, []);
    $action = $urlGen->generate('handle_password_reset', ['token' => $token]);

    return $formBuilder->setAction($action)
      ->setMethod('POST')
      ->getForm()
    ;

  }

}
