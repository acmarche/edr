<?php

namespace AcMarche\Edr\Scolaire\Form;

use AcMarche\Edr\Entity\Scolaire\AnneeScolaire;
use AcMarche\Edr\Entity\Scolaire\GroupeScolaire;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AnneeScolaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add('nom')
            ->add(
                'ordre',
                IntegerType::class,
                [
                ]
            )
            ->add(
                'annee_suivante',
                EntityType::class,
                [
                    'class' => AnneeScolaire::class,
                    'label' => 'Année suivante',
                    'help' => 'Pour le passage d\'année',
                    'required' => false,
                ]
            )
            ->add(
                'groupe_scolaire',
                EntityType::class,
                [
                    'class' => GroupeScolaire::class,
                    'required' => false,
                ]
            )
            ->add('remarque');
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => AnneeScolaire::class,
            ]
        );
    }
}
