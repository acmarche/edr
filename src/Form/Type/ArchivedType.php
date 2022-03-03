<?php

namespace AcMarche\Edr\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ArchivedType extends AbstractType
{
    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'required' => false,
                'label' => 'Archiver',
                'label_attr' => [
                    'class' => 'switch-custom',
                ],
            ]
        );
    }

    public function getParent(): ?string
    {
        return CheckboxType::class;
    }
}
