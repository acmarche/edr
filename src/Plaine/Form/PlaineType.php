<?php

namespace AcMarche\Edr\Plaine\Form;

use AcMarche\Edr\Entity\Plaine\Plaine;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PlaineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add('nom')
            ->add('communication', TextType::class, [
                'required' => true,
                'help' => 'Communication pour les virements bancaires',
            ])
            ->add(
                'prix1',
                MoneyType::class,
                [
                    'required' => true,
                    'label' => 'Prix 1er enfant',
                    'help' => 'Uniquement les chiffres',
                ]
            )
            ->add(
                'prix2',
                MoneyType::class,
                [
                    'required' => true,
                    'label' => 'Prix 2iem enfant',
                    'help' => 'Uniquement les chiffres',
                ]
            )
            ->add(
                'prix3',
                MoneyType::class,
                [
                    'required' => true,
                    'label' => 'Prix 3iem enfant et suivant',
                    'help' => 'Uniquement les chiffres',
                ]
            )
            ->add(
                'plaine_groupes',
                CollectionType::class,
                [
                    'entry_type' => PlaineGroupeEditWithoutFileType::class,
                    'entry_options' => [
                        'label' => false,
                    ],
                    'label' => 'xx',
                ]
            )
            ->add(
                'prematernelle',
                CheckboxType::class,
                [
                    'required' => false,
                    'label' => 'Distinguer les prématernelles pour le listing ?',
                ]
            )
            ->add(
                'remarque',
                TextareaType::class,
                [
                    'required' => false,
                    'attr' => [
                        'rows' => 8,
                    ],
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => Plaine::class,
            ]
        );
    }
}
