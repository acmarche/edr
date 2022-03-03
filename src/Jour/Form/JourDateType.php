<?php

namespace AcMarche\Edr\Jour\Form;

use AcMarche\Edr\Entity\Jour;
use AcMarche\Edr\Form\Type\DateWidgetType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class JourDateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'date_jour',
                DateWidgetType::class,
                [
                    'label' => false,
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => Jour::class,
            ]
        );
    }
}
