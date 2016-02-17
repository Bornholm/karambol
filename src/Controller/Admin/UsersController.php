<?php

namespace Karambol\Controller\Admin;

use Karambol\KarambolApp;
use Karambol\Controller\Controller;
use Karambol\Entity\User;
use Karambol\Form\Type\UserAttributeType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Doctrine\Common\Collections\ArrayCollection;

class UsersController extends Controller {

  public function showUsersIndex() {
    $twig = $this->get('twig');
    $orm = $this->get('orm');
    $users = $orm->getRepository('Karambol\Entity\User')->findAll();
    return $twig->render('admin/users/index.html.twig', [
      'users' => $users
    ]);
  }

  public function showUserEdit($userId) {

    $twig = $this->get('twig');
    $orm = $this->get('orm');

    $user = $orm->getRepository('Karambol\Entity\User')->find($userId);

    $form = $this->getUserForm($user);

    return $twig->render('admin/users/edit.html.twig', [
      'form' => $form->createView()
    ]);

  }

  public function showUsersNew() {
    $twig = $this->get('twig');
    $form = $this->getUserForm();
    return $twig->render('admin/users/edit.html.twig', [
      'form' => $form->createView()
    ]);
  }

  public function handleUserForm() {

    $twig = $this->get('twig');
    $request = $this->get('request');

    $form = $this->getUserForm();

    $form->handleRequest($request);

    if( !$form->isValid()) {
      return $twig->render('admin/users/new.html.twig', [
        'form' => $form->createView()
      ]);
    }

    $user = $form->getData();
    $orm = $this->get('orm');

    if($user->getId() === null) {
      $attributes = $user->getAttributes();
      $user->setAttributes(new ArrayCollection());
      $orm->persist($user);
      foreach($attributes as $attr) {
        $user->addAttribute($attr);
      }
    }

    $orm->flush();

    $urlGen = $this->get('url_generator');
    return $this->redirect($urlGen->generate('admin_users_user_edit', ['userId' => $user->getId()]));

  }

  public function mount(KarambolApp $app) {
    $app->get('/admin/users', [$this, 'showUsersIndex'])->bind('admin_users');
    $app->get('/admin/users/new', [$this, 'showUsersNew'])->bind('admin_users_user_new');
    $app->get('/admin/users/{userId}', [$this, 'showUserEdit'])->bind('admin_users_user_edit');
    $app->post('/admin/users/{userId}', [$this, 'handleUserForm'])
      ->value('userId', null)
      ->bind('admin_users_user_form_handler')
    ;
  }

  protected function getUserForm($user = null) {

    $formFactory = $this->get('form.factory');
    $urlGen = $this->get('url_generator');

    if($user === null) {
      $user = new User();
    }

    $formBuilder = $formFactory->createBuilder(Type\FormType::class, $user);

    return $formBuilder
      ->add('attributes', Type\CollectionType::class, [
        'allow_add' => true,
        'allow_delete' => true,
        'entry_type' => UserAttributeType::class
      ])
      ->add('submit', Type\SubmitType::class)
      ->setAction($urlGen->generate('admin_users_user_form_handler'))
      ->setMethod('POST')
      ->getForm()
    ;

  }

}
