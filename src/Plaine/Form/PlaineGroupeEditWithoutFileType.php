<?php

namespace AcMarche\Edr\Plaine\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

final class PlaineGroupeEditWithoutFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->remove('groupeScolaire');
    }

    public function getParent(): ?string
    {
        return PlaineGroupeType::class;
    }
}
