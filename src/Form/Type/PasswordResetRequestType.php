<?php

namespace Karambol\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Validator\Constraints as Constraints;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

class PasswordResetRequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      $builder
        ->add('email', Type\TextType::class, array(
          'label' => 'password_reset.email',
          'constraints' => [
            new Constraints\Email()
          ]
        ))
        ->add('submit', Type\SubmitType::class, [
          'label' => 'password_reset.request_reset',
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
