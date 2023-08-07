<?php

namespace AcMarche\Edr\Enfant\Form;

use AcMarche\Edr\Entity\Enfant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class EnfantEditForEcoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'accueil_ecole',
                CheckboxType::class,
                [
                    'required' => false,
                    'label' => 'Accueils du matin et soir',
                    'help' => "Cochez cette case si l'enfant vient aux accueils",
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => Enfant::class,
            ]
        );
    }
}
