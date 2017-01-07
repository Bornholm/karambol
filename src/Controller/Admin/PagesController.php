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
namespace Karambol\Controller\Admin;

use Karambol\Controller\AbstractEntityController;
use Karambol\Entity\CustomPage;
use Karambol\Form\Type\CustomPageType;
use Symfony\Component\Form\Extension\Core\Type as Type;

/**
 * Page controller
 * @package Karambol
 * @license AGPLv3
 * @author William Petit
 */
class PagesController extends AbstractEntityController {
  
  /**
   * @todo ecrire phpdoc
   * @return string
   * @author William Petit
   */
  protected function getEntityClass() { return 'Karambol\Entity\CustomPage'; }
  
  /**
   * @todo ecrire phpdoc
   * @return string
   * @author William Petit
   */
  protected function getViewsDirectory() { return 'admin/pages'; }
  
  /**
   * @todo ecrire phpdoc
   * @return string
   * @author William Petit
   */
  protected function getRoutePrefix() { return '/admin/pages'; }
  
  /**
   * @todo ecrire phpdoc
   * @return string
   * @author William Petit
   */
  protected function getRouteNamePrefix() { return 'admin_pages'; }
  
  /**
   * @todo ecrire description
   * @param type $offset
   * @param type $limit
   * @return type
   * @author William Petit
   */
  public function getEntities($offset = 0, $limit = null) {
    return $this->get('pages');
  }
  
  /**
   * @todo ecrire description
   * @param type $form
   * @return type
   * @author William Petit
   */
  protected function saveEntityFromForm($form) {
    $page = $form->getData();
    $orm = $this->get('orm');
    if($page->getId() === null) $orm->persist($page);
    $orm->flush();
    return $page;
  }
  
  /**
   * @todo ecrire description
   * @param type $form
   * @return boolean
   * @author William Petit
   */
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
  
  /**
   * @todo ecrire description
   * @param type $page
   * @return type
   * @author William Petit
   */
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
  
  /**
   * @todo ecrire description 
   * @param CustomPage $page
   * @return type
   * @author William Petit
   */
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
