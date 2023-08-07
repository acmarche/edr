<?php

namespace AcMarche\Edr\Accueil\Form;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class TuteurSubsciberFieldType implements EventSubscriberInterface
{
    public $enfantTuteurRepository;

    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'OnPreSetData',
        ];
    }

    /**
     * Verifie si nouveau objet
     * Si id enfant avec ou pas.
     */
    public function OnPreSetData(FormEvent $event): void
    {
        /** @var Presence $presence */
        $presence = $event->getData();
        $enfant = $presence->getEnfant();

        if ($enfant) {
            $tuteurs = $this->enfantTuteurRepository->getTuteursByEnfant($enfant);
            $form = $event->getForm();

            //new
            if (! $presence || null === $presence->getId()) {
                $form->add(
                    'jours',
                    EntityType::class,
                    [
                        'class' => Jour::class,
                        'multiple' => true,
                        'query_builder' => static fn(JourRepository $cr) => $cr->getForList($enfant),
                        'label' => 'Choisissez une ou plusieurs dates',
                    ]
                );

                if ((is_countable($tuteurs) ? \count($tuteurs) : 0) > 1) {
                    $form->add(
                        'tuteur',
                        EntityType::class,
                        [
                            'choices' => $tuteurs,
                            'class' => Tuteur::class,
                        ]
                    );
                } else {
                    $presence->setTuteur($tuteurs[0]);
                    $form->add('tuteur', HiddenType::class);
                }
            }
        }
    }
}
