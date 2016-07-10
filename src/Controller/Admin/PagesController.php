<?php

namespace Karambol\Controller\Admin;

use Karambol\KarambolApp;
use Karambol\Controller\AbstractEntityController;
use Karambol\Entity\CustomPage;
use Karambol\Form\Type\CustomPageType;
use Symfony\Component\Form\Extension\Core\Type as Type;

class PagesController extends AbstractEntityController {

  protected function getEntityClass() { return 'Karambol\Entity\CustomPage'; }
  protected function getViewsDirectory() { return 'admin/pages'; }
  protected function getRoutePrefix() { return '/admin/pages'; }
  protected function getRouteNamePrefix() { return 'admin_pages'; }

  public function getEntities($offset = 0, $limit = null) {
    return $this->get('pages');
  }

  protected function saveEntityFromForm($form) {
    $page = $form->getData();
    $orm = $this->get('orm');
    if($page->getId() === null) $orm->persist($page);
    $orm->flush();
    return $page;
  }

  protected function deleteEntityFromForm($form) {

    $orm = $this->get('orm');
    $data = $form->getData();

    if(!isset($data['pageId'])) {
      // TODO add flash message to indicate error
      return false;
    }

    $page = $orm->getRepository($this->getEntityClass())->find($data['pageId']);

    if(!$page) {
      // TODO add flash message to indicate error
      return false;
    }

    $orm->remove($page);
    $orm->flush();

    return true;

  }

  protected function getEntityDeleteForm($page) {

    $formFactory = $this->get('form.factory');
    $urlGen = $this->get('url_generator');

    $formBuilder = $formFactory->createBuilder(Type\FormType::class);

    return $formBuilder
      ->add('pageId', Type\HiddenType::class, [
        'data' => $page->getId()
      ])
      ->add('submit', Type\SubmitType::class, [
        'label' => 'admin.pages.delete_page',
        'attr' => [
          'class' => 'btn-danger'
        ]
      ])
      ->setAction($urlGen->generate($this->getRouteName(self::DELETE_ACTION), ['entityId' => $page->getId()]))
      ->setMethod('DELETE')
      ->getForm()
    ;

  }

  protected function getEntityEditForm($page = null) {

    $formFactory = $this->get('form.factory');
    $urlGen = $this->get('url_generator');

    if($page === null) $page = new CustomPage();

    $formBuilder = $formFactory->createBuilder(CustomPageType::class, $page);
    $routeName = $this->getRouteName(self::UPSERT_ACTION);
    $action = $urlGen->generate($routeName, ['entityId' => $page->getId()]);

    return $formBuilder->setAction($action)
      ->setMethod('POST')
      ->getForm()
    ;

  }

}
