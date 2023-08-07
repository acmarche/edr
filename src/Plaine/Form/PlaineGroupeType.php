<?php

namespace AcMarche\Edr\Plaine\Form;

use AcMarche\Edr\Entity\Plaine\PlaineGroupe;
use AcMarche\Edr\Entity\Scolaire\GroupeScolaire;
use AcMarche\Edr\Scolaire\Repository\GroupeScolaireRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PlaineGroupeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'groupeScolaire',
                EntityType::class,
                [
                    'class' => GroupeScolaire::class,
                    'query_builder' => static fn (GroupeScolaireRepository $groupeScolaireRepository) => $groupeScolaireRepository->getQbForListingPlaine(),
                    'attr' => [
                        'readonly' => true,
                    ],
                    'label' => false,
                ]
            )
            ->add('inscription_maximum', IntegerType::class, [
                'label' => "Nombre maximum d'inscrits",
            ]);
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => PlaineGroupe::class,
            ]
        );
    }
}
