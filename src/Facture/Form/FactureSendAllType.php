<?php

namespace AcMarche\Edr\Facture\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

final class FactureSendAllType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->remove('to');
    }

    public function getParent(): ?string
    {
        return FactureSendType::class;
    }
}
