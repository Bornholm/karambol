<?php

namespace Karambol\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Karambol\Entity\PersistentRule;

class RuleType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      $builder->add('comparator');
      $builder->add('comparator');
    }

    public function preSetDataHandler(FormEvent $event) {

      $form = $event->getForm();
      $rule = $event->getData();

    }

    public function configureOptions(OptionsResolver $resolver)
    {
      $resolver->setDefaults([
        'data_class' => 'Karambol\Form\Model\RuleModel',
      ]);
    }
}
