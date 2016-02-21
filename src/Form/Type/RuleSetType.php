<?php

namespace Karambol\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as Type;

class RuleSetType extends AbstractType
{

  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('label', Type\TextType::class, [
        'required' => true
      ])
      ->add('rules', Type\CollectionType::class, [
        'allow_add' => true,
        'allow_delete' => true,
        'entry_type' => RuleType::class,
        'by_reference' => false
      ])
      ->add('submit', Type\SubmitType::class, [
        'label' => 'form.rules.save_ruleset',
        'attr' => [
          'class' => 'btn-success'
        ]
      ])
    ;
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults([
      'data_class' => 'Karambol\Form\Model\RuleSetModel',
      'cascade_validation' => true
    ]);
  }
}
