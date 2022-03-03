<?php

namespace AcMarche\Edr\Plaine\Form;

use AcMarche\Edr\Data\EdrConstantes;
use AcMarche\Edr\Entity\Presence\Presence;
use AcMarche\Edr\Form\Type\OrdreType;
use AcMarche\Edr\Form\Type\RemarqueType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PlainePresenceEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'absent',
                ChoiceType::class,
                [
                    'choices' => array_flip(EdrConstantes::getListAbsences()),
                ]
            )
            ->add(
                'ordre',
                OrdreType::class,
                [
                    'help' => 'En forçant l\ordre, la fratrie présente ne sera pas tenu en compte',
                ]
            )
            ->add('remarque', RemarqueType::class)
            ->add('reduction');
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => Presence::class,
            ]
        );
    }
}
