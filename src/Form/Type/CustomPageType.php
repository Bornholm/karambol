<?php

namespace Karambol\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as Type;

class CustomPageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      $builder
        ->add('url', Type\TextType::class)
        ->add('label', Type\TextType::class)
        ->add('submit', Type\SubmitType::class, [
          'label' => 'form.pages.save_page',
          'attr' => [
            'class' => 'btn-success'
          ]
        ])
      ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
      $resolver->setDefaults([
        'data_class' => 'Karambol\Entity\CustomPage',
        'cascade_validation' => true
      ]);
    }
}
