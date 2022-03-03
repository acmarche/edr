<?php

namespace AcMarche\Edr\Facture\Form;

use AcMarche\Edr\Entity\Facture\Facture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class FacturePayerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'payeLe',
                DateType::class,
                [
                    'label' => 'Date de paiement',
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
                'data_class' => Facture::class,
            ]
        );
    }
}
