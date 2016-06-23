<?php

namespace Karambol\Controller;

use Karambol\KarambolApp;
use Karambol\Form\Type\ProfileType;
use Karambol\Entity\User;
use Karambol\Entity\BaseUser;

class ProfileController extends Controller {

  public function mount(KarambolApp $app) {
    $app->get('/profile', array($this, 'showProfile'))->bind('profile');
    $app->post('/profile', array($this, 'handleProfileForm'))->bind('handle_profile');
  }

  public function showProfile() {

    $twig = $this->get('twig');
    $user = $this->get('user');

    $form = $this->getProfileForm($user);

    return $twig->render('user/profile.html.twig', [
      'profileForm' => $form->createView()
    ]);

  }

  public function handleProfileForm() {

    $twig = $this->get('twig');
    $request = $this->get('request');
    $user = $this->get('user');

    $form = $this->getProfileForm($user);

    $form->handleRequest($request);

    if( !$form->isValid() ) {
      return $twig->render('user/profile.html.twig', [
        'profileForm' => $form->createView()
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

  protected function getProfileForm(BaseUser $user) {

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
