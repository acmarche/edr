<?php

namespace AcMarche\Edr\Ecole\Form;

use AcMarche\Edr\Entity\Scolaire\Ecole;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class EcoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add('nom')
            ->add('abreviation', TextType::class, [
                'label' => 'Abréviation',
                'help' => 'Actuellement utilisé pour la communication de facture',
                'required' => false,
            ])
            ->add(
                'rue',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'code_postal',
                IntegerType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'localite',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add('telephone')
            ->add('gsm')
            ->add(
                'email',
                EmailType::class,
                [
                    'required' => false,
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
                'data_class' => Ecole::class,
            ]
        );
    }
}
