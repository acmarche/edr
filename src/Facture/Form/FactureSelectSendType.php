<?php

namespace AcMarche\Edr\Facture\Form;

use AcMarche\Edr\Form\Type\MonthWidgetType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

final class FactureSelectSendType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'mois',
                MonthWidgetType::class,
                [
                    'required' => true,
                ]
            )
            ->add(
                'mode',
                ChoiceType::class,
                [
                    'placeholder' => 'Par mail ou par papier',
                    'choices' => [
                        'Mail' => 'mail',
                        'Papier' => 'papier',
                    ],
                    'required' => true,
                    'help' => 'Par papier, un pdf sera généré',
                ]
            );
    }
}
