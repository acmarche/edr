<?php

namespace AcMarche\Edr\Accueil\Form;

use AcMarche\Edr\Accueil\Contrat\AccueilInterface;
use AcMarche\Edr\Entity\Presence\Accueil;
use AcMarche\Edr\Form\Type\DateWidgetType;
use AcMarche\Edr\Presence\Form\AddFieldTuteurSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;

final class AccueilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'date_jour',
                DateWidgetType::class,
                [
                    'label' => 'Date',
                ]
            )
            ->add(
                'heure',
                ChoiceType::class,
                [
                    'label' => 'Quand',
                    'placeholder' => 'Matin ou soir',
                    'choices' => array_flip(AccueilInterface::HEURES),
                    'multiple' => false,
                    'expanded' => false,
                    'required' => true,
                ]
            )
            ->add(
                'duree',
                IntegerType::class,
                [
                    'label' => 'Temps resté',
                    'help' => 'Nombre de demi heure que l\'enfant est resté',
                    'constraints' => [
                        new GreaterThan(
                            [
                                'value' => 0,
                            ]
                        ),
                    ],
                ]
            )
            ->add(
                'remarque',
                TextareaType::class,
                [
                    'required' => false,
                    'attr' => [
                        'rows' => 2,
                    ],
                ]
            );
        $formBuilder->addEventSubscriber(new AddFieldTuteurSubscriber());
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => Accueil::class,
            ]
        );
    }
}
