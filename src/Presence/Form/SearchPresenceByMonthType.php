<?php

namespace AcMarche\Edr\Presence\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class SearchPresenceByMonthType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'mois',
                TextType::class,
                [
                    'attr' => [
                        'placeholder' => '05/2020',
                        'autocomplete' => 'off',
                    ],
                    'help' => 'Exemple: 05/2020',
                ]
            );
    }
}
