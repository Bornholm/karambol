<?php

namespace Karambol\Controller;

use Karambol\KarambolApp;
use Karambol\Form\Type\PasswordResetType;

class PasswordController extends Controller {

  public function mount(KarambolApp $app) {
    $app->get('/password/{token}', [$this, 'showPasswordReset'])->bind('password_reset')->value('token', null);
    $app->post('/password', [$this, 'handlePasswordReset'])->bind('handle_password_reset');
  }

  public function showPasswordReset($token = null) {

    $form = $this->getPasswordResetForm($token);

    return $this->render('password_reset/reset.html.twig', [
      'passwordResetForm' => $form->createView()
    ]);
  }

  public function handlePasswordReset() {

    $request = $this->get('request');

    $form = $this->getPasswordResetForm();

    $form->handleRequest($request);

    if(!$form->isValid()) {
      return $this->render('password/reset.html.twig', [
        'passwordResetForm' => $form->createView()
      ]);
    }

    $accounts = $this->get('accounts');
    $user = $this->get('user');



    $this->addFlashMessage('password_reset.successfully_changed_password', ['type' => 'success']);

    return $this->redirect($this->get('url_generator')->generate('home'));

  }

  protected function getPasswordResetForm($token = null) {

    $formFactory = $this->get('form.factory');
    $urlGen = $this->get('url_generator');

    $formBuilder = $formFactory->createBuilder(PasswordResetType::class, ['token' => $token]);
    $action = $urlGen->generate('handle_password_reset');

    return $formBuilder->setAction($action)
      ->setMethod('POST')
      ->getForm()
    ;

  }

}
