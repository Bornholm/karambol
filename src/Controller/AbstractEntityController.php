<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace Karambol\Controller;

use Karambol\KarambolApp;
use Karambol\Controller\Controller;

/**
 * Controlleur
 * @package Karambol
 * @license AGPLv3
 * @author William Petit
 */
abstract class AbstractEntityController extends Controller {
  
  /**
   * Action liste
   * @var string
   */
  const LIST_ACTION = 'list';
  
  /**
   * Action new
   * @var string
   */
  const NEW_ACTION = 'new';
  
  /**
   * Action edition
   * @var string
   */
  const EDIT_ACTION = 'edit';
  
  /**
   * Action delete
   * @var string
   */
  const DELETE_ACTION = 'delete';
  
  /**
   * Action upsert
   * @var string
   */
  const UPSERT_ACTION = 'upsert';
  
  /**
   * Retourne la class de l'entite
   * @author William Petit
   */
  abstract protected function getEntityClass();
  
  /**
   * Retourne le repertoire des vues
   * @author William Petit
   */
  abstract protected function getViewsDirectory();
  
  /**
   * Retourne les prefixes de routes
   * @author William Petit
   */
  abstract protected function getRoutePrefix();
  
  /**
   * Retourne le nom du prefixe
   * @author William Petit
   */
  abstract protected function getRouteNamePrefix();
  
  /**
   * Retourne les entites
   * @author William Petit
   */
  abstract protected function getEntities($offset = 0, $limit = null);
  
  /**
   * Retourne le formulaire d'edition
   * @author William Petit
   */
  abstract protected function getEntityEditForm($entity = null);
  
  /**
   * Retourne le formulaire de suppression
   * @author William Petit
   */
  abstract protected function getEntityDeleteForm($entity);
  
  /**
   * Enregistre l'entite depuis un formulaire
   * @author William Petit
   */
  abstract protected function saveEntityFromForm($form);
  
  /**
   * Supprime l'entite depuis le formulaire
   * @author William Petit
   */
  abstract protected function deleteEntityFromForm($form);

  /**
   * Definition des routes
   * @param KarambolApp $app Application
   * @author William Petit
   */
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
  
  /**
   * Retourne l'identifiant de l'entite
   * @param Entity $entity
   * @return int
   * @author William Petit
   */
  protected function getEntityId($entity) {
    return $entity->getId();
  }

  /**
   * Return the route's name based of the controller's route prefix for the provided action
   * @param string $action the name of the action
   * @return string the route's name
   * @author William Petit
   */
  protected function getRouteName($action) {
    return $this->getRouteNamePrefix().'_'.$action;
  }
  
  /**
   * Voir des entites
   * @param int $offset
   * @param int $limit
   * @return View
   * @author William Petit
   */
  public function showEntities($offset = 0, $limit = null) {
    $twig = $this->get('twig');
    return $twig->render($this->getViewsDirectory().'/index.html.twig', [
      'entities' => $this->getEntities($offset, $limit)
    ]);
  }
  
  /**
   * Editer une entite
   * @param type $entityId
   * @return View
   * @author William Petit
   */
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
  
  /**
   * Formulaire ajout entite
   * @return View
   * @author William Petit
   */
  public function showNewEntity() {
    $twig = $this->get('twig');
    $entityEditForm = $this->getEntityEditForm();
    return $twig->render($this->getViewsDirectory().'/edit.html.twig', [
      'entityEditForm' => $entityEditForm->createView()
    ]);
  }
  
  /**
   * Insert ou met a jour une entite
   * @param int $entityId
   * @return redirect
   */
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
  
  /**
   * Supprime une entite
   * @param int $entityId
   * @return redirect
   * @author William Petit
   */
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
