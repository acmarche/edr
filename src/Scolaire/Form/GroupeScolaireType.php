<?php

namespace AcMarche\Edr\Scolaire\Form;

use AcMarche\Edr\Entity\Scolaire\AnneeScolaire;
use AcMarche\Edr\Entity\Scolaire\GroupeScolaire;
use AcMarche\Edr\Scolaire\Repository\AnneeScolaireRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class GroupeScolaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add('nom')
            ->add(
                'isPlaine',
                CheckboxType::class,
                [
                    'label' => 'Groupe pour les plaines ?',
                    'help' => 'Groupe utilisé pour les plaines',
                    'required' => false,
                ]
            )
            ->add(
                'age_minimum',
                NumberType::class,
                [
                    'label' => 'Âge minimum',
                    'help' => '',
                    'required' => false,
                    'scale' => 1,
                ]
            )
            ->add(
                'age_maximum',
                NumberType::class,
                [
                    'label' => 'Âge maximum',
                    'help' => '',
                    'required' => false,
                    'scale' => 1,
                    'grouping' => false,
                ]
            )
            ->add('remarque')
            ->add(
                'annees_scolaires',
                EntityType::class,
                [
                    'class' => AnneeScolaire::class,
                    'query_builder' => static function (AnneeScolaireRepository $anneeScolaireRepository) {
                        $anneeScolaireRepository->getQbForListing();
                    },
                    'label' => 'Année(s) scolaire(s)',
                    'required' => false,
                    'multiple' => true,
                    'expanded' => true,
                ]
            )
            ->add('ordre', IntegerType::class, [
                'label' => "Ordre d'affichage",
                'help' => 'Ordre dans le listing',
            ]);
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => GroupeScolaire::class,
            ]
        );
    }
}
