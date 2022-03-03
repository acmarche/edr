<?php

namespace AcMarche\Edr\Relation\Form;

use AcMarche\Edr\Enfant\Form\EnfantQuickType;
use AcMarche\Edr\Relation\Dto\TuteurEnfantDto;
use AcMarche\Edr\Tuteur\Form\TuteurQuickType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TuteurEnfantQuickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'tuteur',
                TuteurQuickType::class
            )
            ->add(
                'enfant',
                EnfantQuickType::class,
                [
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => TuteurEnfantDto::class,
            ]
        );
    }
}
