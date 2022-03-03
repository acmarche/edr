<?php

namespace AcMarche\Edr\Facture\Form;

use AcMarche\Edr\Entity\Facture\Creance;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreanceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'nom',
                TextType::class,
                [
                    'label' => 'Intitulé',
                    'required' => true,
                ]
            )
            ->add(
                'montant',
                MoneyType::class,
                [
                    'required' => true,
                    'help' => 'Uniquement les chiffres',
                ]
            )
            ->add(
                'dateLe',
                DateType::class,
                [
                    'label' => 'Date de créance',
                    'widget' => 'single_text',
                    'required' => true,
                    'attr' => [
                        'autocomplete' => 'off',
                    ],
                ]
            )
            ->add(
                'payeLe',
                DateType::class,
                [
                    'label' => 'Date de paiement',
                    'widget' => 'single_text',
                    'required' => false,
                    'attr' => [
                        'autocomplete' => 'off',
                    ],
                ]
            )
            ->add(
                'remarque',
                TextareaType::class,
                [
                    'required' => false,
                    'label' => 'Remarques',
                    'attr' => [
                        'rows' => 5,
                    ],
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => Creance::class,
            ]
        );
    }
}
