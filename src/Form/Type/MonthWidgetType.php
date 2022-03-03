<?php

namespace AcMarche\Edr\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

final class MonthWidgetType extends AbstractType
{
    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'label' => 'Mois',
                'required' => true,
                'attr' => [
                    'placeholder' => '06-2021',
                    'autocomplete' => 'off',
                ],
                'help' => 'Format: 06-2021',
                'constraints' => [new Regex('#^\d{2}-\d{4}$#')],
            ]
        );
    }

    public function getParent(): ?string
    {
        return TextType::class;
    }
}
