<?php

namespace Karambol\Controller\Admin;

use Karambol\KarambolApp;
use Karambol\Controller\Controller;
use Karambol\Entity\CustomPage;
use Karambol\Form\Type\CustomPageType;
use Symfony\Component\Form\Extension\Core\Type as Type;

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

    $pageService = $this->get('page');
    $twig = $this->get('twig');

    $systemPages = $pageService->getSystemPages();
    $customPages = $this->get('orm')
      ->getRepository('Karambol\Entity\CustomPage')
      ->findAll()
    ;

    return $twig->render('admin/pages/index.html.twig', [
      'systemPages' => $systemPages,
      'customPages' => $customPages
    ]);

  }

  public function showPageEdit($pageId) {

    $twig = $this->get('twig');
    $orm = $this->get('orm');

    $page = $orm->getRepository('Karambol\Entity\CustomPage')->find($pageId);

    $pageForm = $this->getPageForm($page);
    $deleteForm = $this->getPageDeleteForm($page->getId());

    return $twig->render('admin/pages/edit.html.twig', [
      'pageForm' => $pageForm->createView(),
      'deleteForm' => $deleteForm->createView(),
      'page' => $page
    ]);

  }

  public function showNewPage() {
    $twig = $this->get('twig');
    $pageForm = $this->getPageForm();
    return $twig->render('admin/pages/edit.html.twig', [
      'pageForm' => $pageForm->createView()
    ]);
  }

  public function handlePageUpsert($pageId) {

    $twig = $this->get('twig');
    $orm = $this->get('orm');
    $request = $this->get('request');

    $page = null;
    if(!empty($pageId)) {
      $user = $orm->getRepository('Karambol\Entity\CustomPage')->find($pageId);
    }

    $form = $this->getPageForm($page);

    $form->handleRequest($request);

    if( !$form->isValid() ) {
      return $twig->render('admin/pages/edit.html.twig', [
        'pageForm' => $form->createView(),
        'deleteForm' => $page ? $this->getPageDeleteForm($page->getId())->createView() : null,
        'page' => $page
      ]);
    }

    $page = $form->getData();
    $orm = $this->get('orm');

    if( $page->getId() === null ) {
      $orm->persist($page);
    }

    $orm->flush();

    $urlGen = $this->get('url_generator');
    return $this->redirect($urlGen->generate('admin_page_edit', ['pageId' => $page->getId()]));

  }

  public function handlePageDelete($pageId) {

    $twig = $this->get('twig');
    $orm = $this->get('orm');
    $request = $this->get('request');

    $page = $orm->getRepository('Karambol\Entity\CustomPage')->find($pageId);
    $deleteForm = $this->getPageDeleteForm($pageId);

    $deleteForm->handleRequest($request);

    if(!$deleteForm->isValid()) {
      return $twig->render('admin/users/edit.html.twig', [
        'pageForm' => $form->createView(),
        'deleteForm' => $deleteForm->createView(),
        'page' => $page
      ]);
    }

    $orm->remove($page);
    $orm->flush();

    $urlGen = $this->get('url_generator');
    return $this->redirect($urlGen->generate('admin_pages'));

  }

  protected function getPageDeleteForm($pageId) {

    $formFactory = $this->get('form.factory');
    $urlGen = $this->get('url_generator');

    $formBuilder = $formFactory->createBuilder(Type\FormType::class);

    return $formBuilder
      ->add('submit', Type\SubmitType::class, [
        'label' => 'admin.pages.delete_page',
        'attr' => [
          'class' => 'btn-danger'
        ]
      ])
      ->setAction($urlGen->generate('admin_page_delete', ['pageId' => $pageId]))
      ->setMethod('DELETE')
      ->getForm()
    ;

  }

  protected function getPageForm($page = null) {

    $formFactory = $this->get('form.factory');
    $urlGen = $this->get('url_generator');

    if($page === null) $page = new CustomPage();

    $formBuilder = $formFactory->createBuilder(CustomPageType::class, $page);
    $action = $urlGen->generate('admin_page_upsert', ['page' => $page->getId()]);

    return $formBuilder->setAction($action)
      ->setMethod('POST')
      ->getForm()
    ;

  }

}
