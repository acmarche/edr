<?php

namespace AcMarche\Edr\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class DateWidgetType extends AbstractType
{
    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'widget' => 'single_text',
                'required' => true,
                'attr' => [
                    'autocomplete' => 'off',
                ],
            ]
        );
    }

    public function getParent(): ?string
    {
        return DateType::class;
    }
}
