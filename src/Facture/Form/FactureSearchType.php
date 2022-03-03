<?php

namespace AcMarche\Edr\Facture\Form;

use AcMarche\Edr\Entity\Plaine\Plaine;
use AcMarche\Edr\Entity\Scolaire\Ecole;
use AcMarche\Edr\Form\Type\DateWidgetType;
use AcMarche\Edr\Form\Type\MonthWidgetType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class FactureSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'numero',
                IntegerType::class,
                [
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Numéro',
                    ],
                ]
            )
            ->add(
                'tuteur',
                SearchType::class,
                [
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Nom du parent',
                    ],
                ]
            )
            ->add(
                'enfant',
                SearchType::class,
                [
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Nom de l\'enfant',
                    ],
                ]
            )
            ->add(
                'mois',
                MonthWidgetType::class,
                [
                    'help' => null,
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Mois facture, format: 06-2021',
                    ],
                ]
            )
            ->add(
                'communication',
                TextType::class,
                [
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Communication',
                    ],
                ]
            )
            ->add(
                'ecole',
                EntityType::class,
                [
                    'class' => Ecole::class,
                    'required' => false,
                    'placeholder' => 'Choisissez une école',
                    'attr' => [
                        'class' => 'custom-select my-1 mr-sm-2',
                    ],
                ]
            )
            ->add(
                'plaine',
                EntityType::class,
                [
                    'class' => Plaine::class,
                    'required' => false,
                    'placeholder' => 'Choisissez une plaine',
                    'attr' => [
                        'class' => 'custom-select my-1 mr-sm-2',
                    ],
                ]
            )
            ->add(
                'paye',
                ChoiceType::class,
                [
                    'label' => 'Payé',
                    'placeholder' => 'Payé ou non',
                    'choices' => [
                        'Payée' => 1,
                        'Non payée' => 0,
                    ],
                    'required' => false,
                ]
            )
            ->add(
                'datePaiement',
                DateWidgetType::class,
                [
                    'required' => false,
                    'help' => 'Date de paiement',
                ]
            );
    }
}
