<?php

namespace Karambol\Controller;

use Karambol\KarambolApp;
use Karambol\Form\Type\ProfileType;
use Karambol\Entity\User;

class ProfileController extends Controller {

  public function mount(KarambolApp $app) {
    $app->get('/profile', array($this, 'showProfile'))->bind('profile');
  }

  public function showProfile() {
    $twig = $this->get('twig');
    $user = $app['user'];
    $form = $this->getProfileForm($user);
    return $twig->render('user/profile.html.twig', [
      'profileForm' => $form->createView()
    ]);
  }

  protected function getProfileForm($user = null) {

    $formFactory = $this->get('form.factory');
    $urlGen = $this->get('url_generator');

    if($user === null) $user = new User();

    $formBuilder = $formFactory->createBuilder(ProfileType::class, $user);
    $action = $urlGen->generate('profile');

    return $formBuilder->setAction($action)
      ->setMethod('POST')
      ->getForm()
    ;

  }

}
