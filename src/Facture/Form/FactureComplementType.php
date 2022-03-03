<?php

namespace AcMarche\Edr\Facture\Form;

use AcMarche\Edr\Entity\Facture\FactureComplement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class FactureComplementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'nom',
                TextType::class,
                [
                    'required' => true,
                    'label' => 'Raison',
                    'help' => 'Expliquez le motif du complément',
                ]
            )
            ->add(
                'pourcentage',
                PercentType::class,
                [
                    'label' => 'Pourcentage',
                    'required' => false,
                    'type' => 'integer',
                    'help' => 'Le pourcentage est appliqué sur le total de la facture',
                ]
            )
            ->add(
                'forfait',
                MoneyType::class,
                [
                    'required' => false,
                    'help' => 'Montant du forfait, uniquement les chiffres',
                ]
            )
            ->add(
                'dateLe',
                DateType::class,
                [
                    'label' => 'Date',
                    'widget' => 'single_text',
                    'required' => true,
                    'attr' => [
                        'autocomplete' => 'off',
                    ],
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => FactureComplement::class,
            ]
        );
    }
}
