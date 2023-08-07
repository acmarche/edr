<?php

namespace AcMarche\Edr\Presence\Form;

use AcMarche\Edr\Ecole\Repository\EcoleRepository;
use AcMarche\Edr\Ecole\Utils\EcoleUtils;
use AcMarche\Edr\Entity\Jour;
use AcMarche\Edr\Entity\Scolaire\Ecole;
use AcMarche\Edr\Jour\Repository\JourRepository;
use AcMarche\Edr\Utils\DateUtils;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

final class SearchPresenceType extends AbstractType
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
                    'query_builder' => static fn (JourRepository $jourRepository) => $jourRepository->getQlNotPlaine(),
                    'choice_label' => static function (Jour $jour): string {
                        $peda = '';
                        if ($jour->isPedagogique()) {
                            $ecoles = EcoleUtils::getNamesEcole($jour->getEcoles());
                            $peda = '(Pédagogique ' . $ecoles . ')';
                        }
                        return ucfirst(DateUtils::formatFr($jour->getDatejour()) . ' ' . $peda);
                    },
                    'group_by' => static fn ($jour, $key, $id) => $jour->getDateJour()->format('Y'),
                ]
            )
            ->add(
                'ecole',
                EntityType::class,
                [
                    'class' => Ecole::class,
                    'query_builder' => static fn (EcoleRepository $ecoleRepository) => $ecoleRepository->getQbForListing(),
                    'required' => false,
                    'placeholder' => 'Choisissez une école',
                    'attr' => [
                        'class' => 'sr-only',
                    ],
                ]
            )
            ->add(
                'displayRemarque',
                CheckboxType::class,
                [
                    'label' => 'Afficher les remarques',
                    'required' => false,
                ]
            );
    }
}
