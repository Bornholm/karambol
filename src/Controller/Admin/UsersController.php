<?php

namespace Karambol\Controller\Admin;

use Karambol\KarambolApp;
use Karambol\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UsersController extends Controller {

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

  public function mount(KarambolApp $app) {
    $app->get('/admin/users', array($this, 'showUsersIndex'))->bind('admin_users');
    $app->get('/admin/users/new', array($this, 'showUsersNew'))->bind('admin_users_new');
    $app->post('/admin/users/new', array($this, 'handleUserForm'))->bind('admin_users_new_handler');
  }

  protected function getUserForm($user = null) {

    $formFactory = $this->get('form.factory');
    $urlGen = $this->get('url_generator');

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
      ->add('submit', SubmitType::class)
      ->setAction($urlGen->generate('admin_users_new_handler'))
      ->setMethod('POST')
      ->getForm()
    ;

  }

}
