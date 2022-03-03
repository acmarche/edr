<?php

namespace AcMarche\Edr\Sante\Form\Etape;

use AcMarche\Edr\Enfant\Form\EnfantEditForParentType;
use AcMarche\Edr\Entity\Enfant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SanteFicheEtape1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => Enfant::class,
            ]
        );
    }

    public function getParent(): ?string
    {
        return EnfantEditForParentType::class;
    }
}
