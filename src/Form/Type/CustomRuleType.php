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

      $codeMirrorOpts = json_encode([
        'lineNumbers' => true,
        'mode' => 'expression-language'
      ]);

      $builder->add('condition', Type\TextAreaType::class, [
        'attr' => [
          'data-codemirror' => $codeMirrorOpts
        ]
      ]);
      $builder->add('action', Type\TextAreaType::class, [
        'attr' => [
          'data-codemirror' => $codeMirrorOpts
        ]
      ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
      $resolver->setDefaults([
        'data_class' => 'Karambol\Entity\CustomRule',
      ]);
    }
}
