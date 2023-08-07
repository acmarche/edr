<?php

namespace AcMarche\Edr\Presence\Form;

use AcMarche\Edr\Data\EdrConstantes;
use AcMarche\Edr\Entity\Presence\Presence;
use AcMarche\Edr\Form\Type\OrdreType;
use AcMarche\Edr\Form\Type\RemarqueType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PresenceType extends AbstractType
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
                    'help' => 'En forçant l\'ordre, la fratrie présente ne sera pas tenue en compte',
                ]
            )
            ->add('remarque', RemarqueType::class)
            ->add('reduction');

        $formBuilder->addEventListener(
            FormEvents::PRE_SET_DATA,
            static function (FormEvent $event): void {
                $form = $event->getForm();
                /** @var Presence $presence */
                $presence = $event->getData();
                $jour = $presence->getJour();
                if ($jour->isPedagogique()) {
                    $form->add(
                        'half',
                        CheckboxType::class,
                        [
                            'label' => 'Demi-journée',
                            'required' => false,
                            'help' => "L'enfant a été présent une demi-journée",
                        ]
                    );
                }
            }
        );

        $formBuilder->addEventSubscriber(new AddFieldTuteurSubscriber());
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
