<?php

namespace AcMarche\Edr\Accueil\Form;

use AcMarche\Edr\Form\Type\DateWidgetType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccueilRetardTpe extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'date_retard',
                DateWidgetType::class,
                [
                    'label' => 'Jour',
                ]
            )
            ->add(
                'heure_retard',
                TimeType::class,
                [
                    'label' => 'Heure de départ',
                    'with_seconds' => false,
                    'hours' => [18, 19],
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                //'data_class' => Accueil::class,
            ]
        );
    }
}
