<?php

namespace Karambol\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      $builder
        ->add('email', Type\TextType::class, [
          'label' => 'profile.email'
        ])
        ->add('password', Type\PasswordType::class, [
          'label' => 'profile.password',
          'always_empty' => true
        ])
        ->add('passwordConfirm', Type\PasswordType::class, [
          'label' => 'profile.password_confirm',
          'always_empty' => true,
          'mapped' => false
        ])
        ->add('submit', Type\SubmitType::class, [
          'label' => 'profile.save_profile',
          'attr' => [
            'class' => 'btn-success'
          ]
        ])
      ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
      $resolver->setDefaults([
        'data_class' => 'Karambol\Entity\User',
        'cascade_validation' => true
      ]);
    }
}
