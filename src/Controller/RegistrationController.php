<?php

namespace Karambol\Controller;

use Karambol\KarambolApp;
use Karambol\Form\Type\RegisterType;

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

    $this->addFlashMessage('registration.successfully_registered', ['type' => 'success']);

    return $this->redirect($this->get('url_generator')->generate('home'));

  }

  protected function getRegisterForm() {

    $formFactory = $this->get('form.factory');
    $urlGen = $this->get('url_generator');

    $formBuilder = $formFactory->createBuilder(RegisterType::class, []);
    $action = $urlGen->generate('handle_register');

    return $formBuilder->setAction($action)
      ->setMethod('POST')
      ->getForm()
    ;

  }

}
