<?php

namespace Karambol\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Karambol\Setting\SettingEntry;
use Karambol\Setting\SettingEntryInterface;

class SettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

      $settings = $options['settings'];

      foreach($settings as $entry) {
        if($entry instanceof SettingEntry) {

          $entryType = $this->detectEntryType($entry);

          $entryOpts = [
            'label' => $entry->getLabel(),
            'data' => $entry->getValue(),
            'constraints' => $entry->getConstraints(),
            'required' => false,
            'attr' => [
              'help' => $entry->getHelp()
            ]
          ];

          if($entryType === Type\ChoiceType::class) {
            $entryOpts['choices'] = $entry->getChoices();
          }

          $builder->add($entry->getName(), $entryType, $entryOpts);

        }
      }

      $builder->add('submit', Type\SubmitType::class, [
        'label' => 'admin.settings.save_settings',
        'attr' => [
          'class' => 'btn-success'
        ]
      ]);

    }

    protected function detectEntryType(SettingEntryInterface $entry) {

      $defaultValue = $entry->getDefaultValue();

      if(is_array($entry->getChoices())) {
        return Type\ChoiceType::class;
      }

      if(is_bool($defaultValue)) {
        return Type\CheckboxType::class;
      }

      return Type\TextType::class;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
      $resolver->setDefaults([
        'cascade_validation' => true,
        'settings' => []
      ]);
    }
}
