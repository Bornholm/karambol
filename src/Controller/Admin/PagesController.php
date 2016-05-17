<?php

namespace Karambol\Controller\Admin;

use Karambol\KarambolApp;
use Karambol\Controller\Controller;
use Karambol\Entity\Page;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Doctrine\Common\Collections\ArrayCollection;

class PagesController extends Controller {

  public function mount(KarambolApp $app) {
    $app->get('/admin/pages', [$this, 'showPages'])->bind('admin_pages');
    $app->get('/admin/pages/new', [$this, 'showNewPage'])->bind('admin_page_new');
    $app->get('/admin/pages/{pageId}', [$this, 'showPageEdit'])->bind('admin_page_edit');
    $app->delete('/admin/pages/{pageId}', [$this, 'handlePageDelete'])
      ->value('pageId', '')
      ->bind('admin_page_delete')
    ;
    $app->post('/admin/pages/{pageId}', [$this, 'handlePageUpsert'])
      ->value('pageId', '')
      ->bind('admin_page_upsert')
    ;
  }

  public function showPages() {

    // $page = new Page();
    // $page->setLabel('Hello world !');
    // $page->setUrl('http://localhost:8080/admin/pages');
    // Page::registerStaticPage($page);
    $pageService = $this->get('page');
    $twig = $this->get('twig');
    return $twig->render('admin/pages/index.html.twig', [
      'pages' => $pageService->getPages()
    ]);
  }

  public function showPageEdit($pageId) {

    $twig = $this->get('twig');
    $orm = $this->get('orm');

    $user = $orm->getRepository('Karambol\Entity\User')->find($userId);

    $userForm = $this->getUserForm($user);
    $deleteForm = $this->getUserDeleteForm($user->getId());

    return $twig->render('admin/users/edit.html.twig', [
      'userForm' => $userForm->createView(),
      'deleteForm' => $deleteForm->createView(),
      'user' => $user
    ]);

  }

  public function showNewUserForm() {
    $twig = $this->get('twig');
    $userForm = $this->getUserForm();
    return $twig->render('admin/users/edit.html.twig', [
      'userForm' => $userForm->createView()
    ]);
  }

  public function handleUserUpsert($userId) {

    $twig = $this->get('twig');
    $orm = $this->get('orm');
    $request = $this->get('request');

    $user = null;
    if(!empty($userId)) {
      $user = $orm->getRepository('Karambol\Entity\User')->find($userId);
    }

    $form = $this->getUserForm($user);

    $form->handleRequest($request);

    if( !$form->isValid() ) {
      return $twig->render('admin/users/edit.html.twig', [
        'userForm' => $form->createView(),
        'deleteForm' => $user ? $this->getUserDeleteForm($user->getId())->createView() : null,
        'user' => $user
      ]);
    }

    $user = $form->getData();
    $orm = $this->get('orm');

    if( $user->getId() === null ) {
      $orm->persist($user);
    }

    $orm->flush();

    $urlGen = $this->get('url_generator');
    return $this->redirect($urlGen->generate('admin_user_edit', ['userId' => $user->getId()]));

  }

  public function handleUserDelete($userId) {

    $twig = $this->get('twig');
    $orm = $this->get('orm');
    $request = $this->get('request');

    $user = $orm->getRepository('Karambol\Entity\User')->find($userId);
    $deleteForm = $this->getUserDeleteForm($userId);

    $deleteForm->handleRequest($request);

    if(!$deleteForm->isValid()) {
      return $twig->render('admin/users/edit.html.twig', [
        'userForm' => $form->createView(),
        'deleteForm' => $deleteForm->createView(),
        'user' => $user
      ]);
    }

    $orm->remove($user);
    $orm->flush();

    $urlGen = $this->get('url_generator');
    return $this->redirect($urlGen->generate('admin_users'));

  }

  protected function getUserDeleteForm($userId) {

    $formFactory = $this->get('form.factory');
    $urlGen = $this->get('url_generator');

    $formBuilder = $formFactory->createBuilder(Type\FormType::class);

    return $formBuilder
      ->add('submit', Type\SubmitType::class, [
        'label' => 'admin.users.delete_user',
        'attr' => [
          'class' => 'btn-danger'
        ]
      ])
      ->setAction($urlGen->generate('admin_user_delete', ['userId' => $userId]))
      ->setMethod('DELETE')
      ->getForm()
    ;

  }

  protected function getUserForm($user = null) {

    $formFactory = $this->get('form.factory');
    $urlGen = $this->get('url_generator');

    if($user === null) $user = new User();

    $formBuilder = $formFactory->createBuilder(UserType::class, $user);
    $action = $urlGen->generate('admin_user_upsert', ['userId' => $user->getId()]);

    return $formBuilder->setAction($action)
      ->setMethod('POST')
      ->getForm()
    ;

  }

}
