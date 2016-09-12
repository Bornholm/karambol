<?php

namespace Karambol\Controller\Admin;

use Karambol\KarambolApp;
use Karambol\Controller\AbstractEntityController;
use Karambol\Entity\User;
use Karambol\Form\Type\UserType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Doctrine\Common\Collections\ArrayCollection;

class UsersController extends AbstractEntityController {

  protected function getEntityClass() { return User::class; }
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

    $userCreation = false;

    if($user->getId() === null) {
      $orm->persist($user);
      $userCreation = true;
    }

    $orm->flush();

    $this->addFlashMessage(
      $userCreation ? 'admin.users.user_created' : 'admin.users.user_saved',
      ['type' => 'success']
    );

    return $user;

  }

  protected function deleteEntityFromForm($form) {

    $orm = $this->get('orm');
    $data = $form->getData();

    if(!isset($data['userId'])) {
      $this->addFlashMessage('admin.users.invalid_data', ['type' => 'error']);
      return false;
    }

    $user = $orm->getRepository($this->getEntityClass())->find($data['userId']);

    if(!$user) {
      $this->addFlashMessage('admin.users.user_not_found', ['type' => 'error']);
      return false;
    }

    $orm->remove($user);
    $orm->flush();

    $this->addFlashMessage('admin.users.user_deleted', ['type' => 'success']);

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

    $formBuilder = $formFactory->createBuilder(UserType::class, $user);
    $action = $urlGen->generate($this->getRouteName(self::UPSERT_ACTION), ['entityId' => $user->getId()]);

    return $formBuilder->setAction($action)
      ->setMethod('POST')
      ->getForm()
    ;

  }

}
