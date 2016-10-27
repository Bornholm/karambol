<?php

namespace Karambol\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Karambol\Entity\CustomRule;
use Symfony\Component\Form\Extension\Core\DataTransformer\IntegerToLocalizedStringTransformer;
use Symfony\Component\Validator\Constraints as Constraints;


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
          'data-codemirror' => $codeMirrorOpts,
          'constraints' => [
            new Constraints\NotBlank()
          ]
        ]
      ]);
      $builder->add('action', Type\TextAreaType::class, [
        'attr' => [
          'data-codemirror' => $codeMirrorOpts
        ],
        'constraints' => [
          new Constraints\NotBlank()
        ]
      ]);

      $builder->add('weight', Type\IntegerType::class, [
        'scale' => 0,
        'rounding_mode' => IntegerToLocalizedStringTransformer::ROUND_DOWN,
        'constraints' => [
          new Constraints\Range(['min' => 0])
        ],
        'attr' => [
          'min' => 0
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
