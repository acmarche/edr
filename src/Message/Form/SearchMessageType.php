<?php

namespace AcMarche\Edr\Message\Form;

use AcMarche\Edr\Ecole\Repository\EcoleRepository;
use AcMarche\Edr\Entity\Jour;
use AcMarche\Edr\Entity\Plaine\Plaine;
use AcMarche\Edr\Entity\Scolaire\Ecole;
use AcMarche\Edr\Jour\Repository\JourRepository;
use AcMarche\Edr\Plaine\Repository\PlaineRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

final class SearchMessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'jour',
                EntityType::class,
                [
                    'class' => Jour::class,
                    'placeholder' => 'Choisissez une date',
                    'required' => false,
                    'query_builder' => static fn (JourRepository $jourRepository) => $jourRepository->getQlNotPlaine(),
                    //todo display name day
                    'group_by' => static fn ($jour, $key, $id) => $jour->getDateJour()->format('Y'),
                ]
            )
            ->add(
                'ecole',
                EntityType::class,
                [
                    'required' => false,
                    'placeholder' => 'Choisissez une école',
                    'attr' => [
                        'class' => 'sr-only',
                    ],
                    'class' => Ecole::class,
                    'query_builder' => static fn (EcoleRepository $ecoleRepository) => $ecoleRepository->getQbForListing(),
                ]
            )
            ->add(
                'plaine',
                EntityType::class,
                [
                    'required' => false,
                    'placeholder' => 'Choisissez une plaine',
                    'attr' => [
                        'class' => 'sr-only',
                    ],
                    'class' => Plaine::class,
                    'query_builder' => static fn (PlaineRepository $plaineRepository) => $plaineRepository->getQbForListing(),
                ]
            );
    }
}
