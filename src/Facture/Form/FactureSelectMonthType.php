<?php

namespace AcMarche\Edr\Facture\Form;

use AcMarche\Edr\Form\Type\MonthWidgetType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

final class FactureSelectMonthType extends AbstractType
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
            );
    }
}
