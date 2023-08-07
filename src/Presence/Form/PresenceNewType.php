<?php

namespace AcMarche\Edr\Presence\Form;

use AcMarche\Edr\Ecole\Utils\EcoleUtils;
use AcMarche\Edr\Entity\Jour;
use AcMarche\Edr\Jour\Repository\JourRepository;
use AcMarche\Edr\Presence\Dto\PresenceSelectDays;
use AcMarche\Edr\Utils\DateUtils;
use DateTime;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PresenceNewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $enfant = $formBuilder->getData()->getEnfant();
        $date = new DateTime();
        $date->modify('-2 Years');

        $formBuilder
            ->add(
                'jours',
                EntityType::class,
                [
                    'class' => Jour::class,
                    'multiple' => true,
                    'query_builder' => static fn(JourRepository $cr) => $cr->getQlJourByDateGreatherOrEqualAndNotRegister(
                        $enfant,
                        $date
                    ),
                    'label' => 'Sélectionnez une ou plusieurs dates',
                    'choice_label' => static function (Jour $jour) : string {
                        $peda = '';
                        if ($jour->isPedagogique()) {
                            $ecoles = EcoleUtils::getNamesEcole($jour->getEcoles());
                            $peda = '(Pédagogique '.$ecoles.')';
                        }
                        return ucfirst(DateUtils::formatFr($jour->getDatejour()).' '.$peda);
                    },
                    'attr' => [
                        'style' => 'height:150px;',
                    ],
                    'group_by' => static fn($date) => $date->getDateJour()->format('m').'-'.$date->getDateJour()->format('Y'),
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => PresenceSelectDays::class,
            ]
        );
    }
}
