<?php

namespace AcMarche\Edr\Plaine\Form;

use AcMarche\Edr\Entity\Plaine\Plaine;
use AcMarche\Edr\Jour\Form\JourDateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PlaineJoursType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'jours',
                CollectionType::class,
                [
                    'entry_type' => JourDateType::class,
                    'entry_options' => [
                        'label' => false,
                    ],
                    'allow_add' => true,
                    'allow_delete' => true,
                    'required' => false,
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
