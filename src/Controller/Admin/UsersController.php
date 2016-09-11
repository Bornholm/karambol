<?php

namespace Karambol\Controller\Admin;

use Karambol\KarambolApp;
use Karambol\Controller\AbstractEntityController;
use Karambol\Entity\User;
use Karambol\Form\Type\BaseUserType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Doctrine\Common\Collections\ArrayCollection;

class UsersController extends AbstractEntityController {

  protected function getEntityClass() { return $this->get('user_entity'); }
  protected function getViewsDirectory() { return 'admin/users'; }
  protected function getRoutePrefix() { return '/admin/users'; }
  protected function getRouteNamePrefix() { return 'admin_users'; }

  public function getEntities($offset = 0, $limit = null) {

    $orm = $this->get('orm');
    $qb = $orm->getRepository($this->getEntityClass())->createQueryBuilder('u');

    $qb->setFirstResult($offset);
    if($limit !== null) $qb->setMaxResults($limit);

    return $qb->getQuery()->getResult();

  }

  protected function saveEntityFromForm($form) {

    $orm = $this->get('orm');
    $user = $form->getData();

    if($user->getId() === null) {
      $orm->persist($user);
    }

    $orm->flush();

    $this->addFlashMessage('User saved.');

    return $user;

  }

  protected function deleteEntityFromForm($form) {

    $orm = $this->get('orm');
    $data = $form->getData();

    if(!isset($data['userId'])) {
      // TODO add flash message to indicate error
      return false;
    }

    $user = $orm->getRepository($this->getEntityClass())->find($data['userId']);

    if(!$user) {
      // TODO add flash message to indicate error
      return false;
    }

    $orm->remove($user);
    $orm->flush();

    return true;

  }

  protected function getEntityDeleteForm($user) {

    $formFactory = $this->get('form.factory');
    $urlGen = $this->get('url_generator');

    $formBuilder = $formFactory->createBuilder(Type\FormType::class);

    return $formBuilder
      ->add('userId', Type\HiddenType::class, [
        'data' => $user->getId()
      ])
      ->add('submit', Type\SubmitType::class, [
        'label' => 'admin.users.delete_user',
        'attr' => [
          'class' => 'btn-danger'
        ]
      ])
      ->setAction($urlGen->generate($this->getRouteName(self::DELETE_ACTION), ['entityId' => $user->getId()]))
      ->setMethod('DELETE')
      ->getForm()
    ;

  }

  protected function getEntityEditForm($user = null) {

    $formFactory = $this->get('form.factory');
    $urlGen = $this->get('url_generator');
    $userEntity = $this->getEntityClass();

    if($user === null) $user = new $userEntity();

    $formBuilder = $formFactory->createBuilder(BaseUserType::class, $user);
    $action = $urlGen->generate($this->getRouteName(self::UPSERT_ACTION), ['entityId' => $user->getId()]);

    return $formBuilder->setAction($action)
      ->setMethod('POST')
      ->getForm()
    ;

  }

}
