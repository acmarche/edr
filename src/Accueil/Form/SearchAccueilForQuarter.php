<?php

namespace AcMarche\Edr\Accueil\Form;

use AcMarche\Edr\Entity\Scolaire\Ecole;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

final class SearchAccueilForQuarter extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'trimestre',
                ChoiceType::class,
                [
                    'label' => 'Trimestre',
                    'choices' => [
                        'Premier trimestre' => 1,
                        'Deuxième trimestre' => 2,
                        'Troisième trimestre' => 3,
                        'Quatrième trimestre' => 4,
                    ],
                    'required' => true,
                ]
            )
            ->add(
                'ecole',
                EntityType::class,
                [
                    'class' => Ecole::class,
                    'required' => true,
                ]
            )
            ->add(
                'year',
                IntegerType::class,
                [
                    'label' => 'Année',
                    'required' => true,
                ]
            );
    }
}
