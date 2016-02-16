<?php

namespace Karambol\Controller;

use Karambol\KarambolApp;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class AdminController extends Controller {

  public function showAdminIndex() {
    $twig = $this->get('twig');
    return $twig->render('admin/index.html.twig');
  }

  public function showUsersIndex() {
    $twig = $this->get('twig');
    $orm = $this->get('orm');
    $users = $orm->getRepository('Karambol\Entity\User')->findAll();
    return $twig->render('admin/users/index.html.twig', [
      'users' => $users
    ]);
  }

  public function showUsersNew() {
    $twig = $this->get('twig');
    $form = $this->getUserForm();
    return $twig->render('admin/users/new.html.twig', [
      'form' => $form->createView()
    ]);
  }

  public function handleUserForm() {
    $twig = $this->get('twig');
    $form = $this->getUserForm();
    return $twig->render('admin/users/new.html.twig', [
      'form' => $form->createView()
    ]);
  }

  protected function _mount(KarambolApp $app) {
    $app->get('/admin', array($this, 'showAdminIndex'))->bind('admin');
    $app->get('/admin/users', array($this, 'showUsersIndex'))->bind('admin_users');
    $app->get('/admin/users/new', array($this, 'showUsersNew'))->bind('admin_users_new');
    $this->composeMainMenu();
  }

  protected function getUserForm($user = null) {

    $formFactory = $this->get('form.factory');

    if($user === null) {
      $user = [];
    }

    return $formFactory->createBuilder(FormType::class, $user)
      ->add('name')
      ->add('email')
      ->add('gender', ChoiceType::class, array(
        'choices' => array(1 => 'male', 2 => 'female'),
        'expanded' => true
      ))
      ->getForm()
    ;

  }

  protected function composeMainMenu() {

    $menu = $this->get('menu');

    $menu->additem('admin_main', [
      'label' => 'admin.navbar.users',
      'route' => 'admin_users'
    ]);

  }

}
