<?php

namespace Karambol\Controller;

use Karambol\KarambolApp;
use Karambol\Controller\Controller;
use Karambol\Entity\CustomPage;
use Karambol\Form\Type\CustomPageType;
use Symfony\Component\Form\Extension\Core\Type as Type;

abstract class AbstractEntityController extends Controller {

  const LIST_ACTION = 'list';
  const NEW_ACTION = 'new';
  const EDIT_ACTION = 'edit';
  const DELETE_ACTION = 'delete';
  const UPSERT_ACTION = 'upsert';

  abstract protected function getEntityClass();
  abstract protected function getViewsDirectory();
  abstract protected function getRoutePrefix();
  abstract protected function getRouteNamePrefix();

  abstract protected function getEntities($offset = 0, $limit = null);
  abstract protected function getEntityEditForm($entity = null);
  abstract protected function getEntityDeleteForm($entity);
  abstract protected function saveEntityFromForm($form);
  abstract protected function deleteEntityFromForm($form);


  public function mount(KarambolApp $app) {

    $routePrefix = $this->getRoutePrefix();

    $app->get($routePrefix, [$this, 'showEntities'])
      ->bind($this->getRouteName(self::LIST_ACTION))
    ;

    $app->get($routePrefix.'/new', [$this, 'showNewEntity'])
      ->bind($this->getRouteName(self::NEW_ACTION));
    $app->get($routePrefix.'/{entityId}', [$this, 'showEntityEdit'])->bind($this->getRouteName(self::EDIT_ACTION));
    $app->delete($routePrefix.'/{entityId}', [$this, 'handleEntityDelete'])
      ->value('entityId', '')
      ->bind($this->getRouteName(self::DELETE_ACTION))
    ;
    $app->post($routePrefix.'/{entityId}', [$this, 'handleEntityUpsert'])
      ->value('entityId', '')
      ->bind($this->getRouteName(self::UPSERT_ACTION))
    ;

  }

  protected function getEntityId($entity) {
    return $entity->getId();
  }

  protected function getRouteName($action = '') {
    return $this->getRouteNamePrefix().'_'.$action;
  }

  public function showEntities($offset = 0, $limit = null) {
    $twig = $this->get('twig');
    return $twig->render($this->getViewsDirectory().'/index.html.twig', [
      'entities' => $this->getEntities($offset, $limit)
    ]);
  }

  public function showEntityEdit($entityId) {

    $twig = $this->get('twig');
    $orm = $this->get('orm');

    $entity = $orm->getRepository($this->getEntityClass())->find($entityId);

    $entityEditForm = $this->getEntityEditForm($entity);
    $entityDeleteForm = $this->getEntityDeleteForm($entity);

    return $twig->render($this->getViewsDirectory().'/edit.html.twig', [
      'entityEditForm' => $entityEditForm->createView(),
      'entityDeleteForm' => $entityDeleteForm->createView(),
      'entity' => $entity
    ]);

  }

  public function showNewEntity() {
    $twig = $this->get('twig');
    $entityEditForm = $this->getEntityEditForm();
    return $twig->render($this->getViewsDirectory().'/edit.html.twig', [
      'entityEditForm' => $entityEditForm->createView()
    ]);
  }

  public function handleEntityUpsert($entityId) {

    $twig = $this->get('twig');
    $orm = $this->get('orm');
    $request = $this->get('request');

    $entity = null;
    if(!empty($entityId)) {
      $entity = $orm->getRepository($this->getEntityClass())->find($entityId);
    }

    $entityEditForm = $this->getEntityEditForm($entity);

    $entityEditForm->handleRequest($request);

    if( !$entityEditForm->isValid() ) {
      return $twig->render($this->getViewsDirectory().'/edit.html.twig', [
        'entityEditForm' => $entityEditForm->createView(),
        'entityDeleteForm' => $entity ? $this->getEntityDeleteForm($entity)->createView() : null,
        'entity' => $entity
      ]);
    }

    $entity = $this->saveEntityFromForm($entityEditForm);

    if(!$entity) {
      return $twig->render($this->getViewsDirectory().'/edit.html.twig', [
        'entityEditForm' => $entityEditForm->createView(),
        'entityDeleteForm' => $entity ? $this->getEntityDeleteForm($entity)->createView() : null,
        'entity' => $entity
      ]);
    }

    $urlGen = $this->get('url_generator');
    $redirectUrl = $urlGen->generate($this->getRouteName(self::EDIT_ACTION), ['entityId' => $this->getEntityId($entity)]);
    return $this->redirect($redirectUrl);

  }

  public function handleEntityDelete($entityId) {

    $twig = $this->get('twig');
    $orm = $this->get('orm');
    $request = $this->get('request');

    $entity = $orm->getRepository($this->getEntityClass())->find($entityId);
    $entityDeleteForm = $this->getEntityDeleteForm($entity);

    $entityDeleteForm->handleRequest($request);

    if(!$entityDeleteForm->isValid()) {
      return $twig->render($this->getViewsDirectory().'/edit.html.twig', [
        'entityEditForm' => $this->getEntityEditForm($entity),
        'entityDeleteForm' => $entityDeleteForm->createView(),
        'entity' => $entity
      ]);
    }

    $deleted = $this->deleteEntityFromForm($entityDeleteForm);

    if(!$deleted) {
      return $twig->render($this->getViewsDirectory().'/edit.html.twig', [
        'entityEditForm' => $this->getEntityEditForm($entity)->createView(),
        'entityDeleteForm' => $entityDeleteForm->createView(),
        'entity' => $entity
      ]);
    }

    $urlGen = $this->get('url_generator');
    $redirectUrl = $urlGen->generate($this->getRouteName(self::LIST_ACTION));
    return $this->redirect($redirectUrl);

  }

}
