<?php

namespace AcMarche\Edr\Facture\Form;

use AcMarche\Edr\Entity\Facture\Facture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class FactureAttachType extends AbstractType
{
    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => Facture::class,
            ]
        );
    }
}
