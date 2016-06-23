<?php

namespace Karambol\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Validator\Constraints as Assert;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      $builder
        ->add('email', Type\TextType::class, [
          'label' => 'profile.email',
          'constraints' => [
            new Assert\Email()
          ]
        ])
        ->add('password', Type\RepeatedType::class, array(
          'type' => Type\PasswordType::class,
          'required' => false,
          'mapped' => false,
          'first_options'  => ['label' => 'profile.password'],
          'second_options' => ['label' => 'profile.password_confirm']
        ))
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
