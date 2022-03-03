<?php

namespace AcMarche\Edr\Sante\Form\Etape;

use AcMarche\Edr\Entity\Sante\SanteFiche;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\NotBlank;

final class SanteFicheEtape2Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $countConstraint = new Count(0, 1);
        $countConstraint->minMessage = 'Il faut au moins un accompagnateur';

        $formBuilder
            ->add(
                'personne_urgence',
                TextareaType::class,
                [
                    'label' => 'Personne(s) en cas d\'urgence',
                    'help' => 'Nom, prénom et numéro de téléphone',
                ]
            )
            ->add(
                'medecin_nom',
                TextType::class,
                [
                    'label' => 'Nom du médecin',
                ]
            )
            ->add(
                'medecin_telephone',
                TextType::class,
                [
                    'label' => 'Téléphone du médecin',
                ]
            )
            ->add(
                'accompagnateurs',
                CollectionType::class,
                [
                    'entry_type' => TextType::class,
                    'entry_options' => [
                        'label' => false,
                        'constraints' => [
                            new NotBlank(),
                        ],
                    ],
                    'prototype' => true,
                    'required' => true,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'label' => 'Personnes autorisées à reprendre l’enfant dans les accueils',
                    'help' => 'Nom et téléphone',
                    'constraints' => [
                        $countConstraint,
                    ],
                ]
            )
            ->add(
                'remarque',
                TextareaType::class,
                [
                    'required' => false,
                    'label' => "D'autres remarques",
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => SanteFiche::class,
            ]
        );
    }
}
