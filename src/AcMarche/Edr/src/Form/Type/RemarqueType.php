<?php

namespace AcMarche\Edr\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class RemarqueType extends AbstractType
{
    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'attr' => [
                    'rows' => 3,
                ],
                'required' => false,
            ]
        );
    }

    public function getParent(): ?string
    {
        return TextareaType::class;
    }
}
