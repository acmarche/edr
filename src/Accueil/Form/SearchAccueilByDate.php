<?php

namespace AcMarche\Edr\Accueil\Form;

use AcMarche\Edr\Accueil\Contrat\AccueilInterface;
use AcMarche\Edr\Entity\Scolaire\Ecole;
use AcMarche\Edr\Form\Type\DateWidgetType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SearchAccueilByDate extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'date_jour',
                DateWidgetType::class,
                [
                    'label' => 'Date',
                    'required' => true,
                ]
            )
            ->add(
                'heure',
                ChoiceType::class,
                [
                    'label' => 'Quand',
                    'placeholder' => 'Matin ou soir',
                    'choices' => array_flip(AccueilInterface::HEURES),
                    'required' => false,
                ]
            )
            ->add(
                'ecole',
                EntityType::class,
                [
                    'class' => Ecole::class,
                    'required' => false,
                ]
            )
            ->add('groupEcole', CheckboxType::class, [
                'label' => 'Grouper par école',
                    'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
            ]
        );
    }
}
