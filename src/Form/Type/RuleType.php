<?php

namespace Karambol\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Karambol\Entity\PersistentRule;
use Karambol\RuleEngine\Rule\PropertyTestRule;

class RuleType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      $builder->add('propertyPath', Type\TextType::class, [

      ]);
      $builder->add('comparator', Type\ChoiceType::class, [
        'choices' => [
          'rules.comparator.lt' => PropertyTestRule::LT,
          'rules.comparator.lte' => PropertyTestRule::LTE,
          'rules.comparator.eq' => PropertyTestRule::EQ,
          'rules.comparator.gt' => PropertyTestRule::GT,
          'rules.comparator.gte' => PropertyTestRule::GTE,
          'rules.comparator.match' => PropertyTestRule::MATCH
        ]
      ]);
      $builder->add('criteria');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
      $resolver->setDefaults([
        'data_class' => 'Karambol\Form\Model\RuleModel',
      ]);
    }
}
