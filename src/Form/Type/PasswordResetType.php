<?php

namespace Karambol\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Validator\Constraints as Constraints;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

class PasswordResetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      $builder
        ->add('password', Type\RepeatedType::class, array(
          'type' => Type\PasswordType::class,
          'required' => false,
          'mapped' => false,
          'first_options'  => ['label' => 'password_reset.new_password'],
          'second_options' => ['label' => 'password_reset.new_password_confirm']
        ))
        ->add('submit', Type\SubmitType::class, [
          'label' => 'password_reset.submit_reset',
          'attr' => [
            'class' => 'btn-success'
          ]
        ])
      ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
      $resolver->setDefaults([
        'cascade_validation' => true
      ]);
    }
}
