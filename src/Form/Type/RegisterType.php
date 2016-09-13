<?php

namespace Karambol\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Validator\Constraints as Constraints;

class RegisterType extends AbstractType
{

  public function buildForm(FormBuilderInterface $builder, array $options) {

    $translator = $options['translator'];

    $builder
      ->add('username', Type\TextType::class, [
        'label' => 'registration.username'
      ])
      ->add('email', Type\RepeatedType::class, array(
        'type' => Type\TextType::class,
        'first_options'  => ['label' => 'registration.email'],
        'second_options' => ['label' => 'registration.email_confirm'],
        'invalid_message' => $translator->trans('registration.emails_must_be_the_same'),
        'constraints' => [
          new Constraints\Email()
        ]
      ))
      ->add('submit', Type\SubmitType::class, [
        'label' => 'registration.register',
        'attr' => [
          'class' => 'btn-success'
        ]
      ])
    ;
    
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults([
      'cascade_validation' => true,
      'translator' => null
    ]);
  }
}
