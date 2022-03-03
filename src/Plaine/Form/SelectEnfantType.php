<?php

namespace AcMarche\Edr\Plaine\Form;

use AcMarche\Edr\Entity\Enfant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;

class SelectEnfantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder->add(
            'enfants',
            EntityType::class,
            [
                'class' => Enfant::class,
                'choices' => $options['enfants'],
                'multiple' => true,
                'expanded' => true,
                'constraints' => [
                    new Count([
                        'min' => 1,
                        'minMessage' => 'Sélectionnez au moins un enfant',
                    ]),
                ],
            ]
        );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setRequired(
            [
                'enfants',
            ]
        );
        $optionsResolver->setAllowedTypes('enfants', 'array');
    }
}
