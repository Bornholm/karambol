<?php

namespace Karambol\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Karambol\Entity\CustomRule;

class CustomRuleType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      $builder->add('condition');
      $builder->add('action');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
      $resolver->setDefaults([
        'data_class' => 'Karambol\Entity\CustomRule',
      ]);
    }
}
