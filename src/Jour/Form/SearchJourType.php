<?php

namespace AcMarche\Edr\Jour\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

final class SearchJourType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'archived',
                CheckboxType::class,
                [
                    'label' => 'Afficher les jours archivés',
                    'required' => false,
                    'label_attr' => [
                        'class' => 'switch-custom',
                    ],
                ]
            )
            ->add(
                'pedagogique',
                CheckboxType::class,
                [
                    'label' => 'Journée pédagoque',
                    'required' => false,
                    'label_attr' => [
                        'class' => 'switch-custom',
                    ],
                ]
            );
    }
}
