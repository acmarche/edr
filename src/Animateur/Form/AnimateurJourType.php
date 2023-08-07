<?php

namespace AcMarche\Edr\Animateur\Form;

use AcMarche\Edr\Ecole\Utils\EcoleUtils;
use AcMarche\Edr\Entity\Animateur;
use AcMarche\Edr\Entity\Jour;
use AcMarche\Edr\Jour\Repository\JourRepository;
use AcMarche\Edr\Utils\DateUtils;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AnimateurJourType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'jours',
                EntityType::class,
                [
                    'class' => Jour::class,
                    'placeholder' => "Jours d'accueil",
                    'query_builder' => static fn(JourRepository $jourRepository) => $jourRepository->getQlNotPlaine(),
                    'group_by' => static fn($jour, $key, $id) => $jour->getDateJour()->format('Y'),
                    'required' => false,
                    'choice_label' => static function (Jour $jour) : string {
                        $peda = '';
                        if ($jour->isPedagogique()) {
                            $ecoles = EcoleUtils::getNamesEcole($jour->getEcoles());
                            $peda = '(Pédagogique '.$ecoles.')';
                        }
                        return ucfirst(DateUtils::formatFr($jour->getDatejour()).' '.$peda);
                    },
                    'multiple' => true,
                    'expanded' => true,
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => Animateur::class,
            ]
        );
    }
}
