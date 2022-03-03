<?php

namespace AcMarche\Edr\Relation\Utils;

use AcMarche\Edr\Contrat\Presence\PresenceInterface;
use AcMarche\Edr\Entity\Enfant;
use AcMarche\Edr\Entity\Presence\Presence;
use AcMarche\Edr\Entity\Tuteur;
use AcMarche\Edr\Presence\Repository\PresenceRepository;
use AcMarche\Edr\Relation\Repository\RelationRepository;
use AcMarche\Edr\Utils\SortUtils;

final class OrdreService
{
    public function __construct(
        private RelationRepository $relationRepository,
        private PresenceRepository $presenceRepository
    ) {
    }

    /**
     * Ordre de l'enfant par importance decroissante.
     */
    public function getOrdreOnRelation(Enfant $enfant, Tuteur $tuteur): ?int
    {
        $relation = $this->relationRepository->findOneByTuteurAndEnfant($tuteur, $enfant);
        if (null !== $relation && 0 !== $relation->getOrdre()) {
            return $relation->getOrdre();
        }

        return null;
    }

    public function getOrdreOnPresence(PresenceInterface $presence): int
    {
        /*
         * Ordre force sur la presence
         */
        if (0 !== ($presence->getOrdre())) {
            return $presence->getOrdre();
        }

        $tuteur = $presence->getTuteur();
        $enfant = $presence->getEnfant();
        $ordreBase = $enfant->getOrdre();
        if ($ordreRelation = $this->getOrdreOnRelation($enfant, $tuteur)) {
            $ordreBase = $ordreRelation;
        }
        /*
         * quand enfant premier, fratrie pas d'importance
         */
        if (1 === $ordreBase) {
            return $ordreBase;
        }

        /**
         * Ordre suivant la fratries.
         */
        $fratries = $this->relationRepository->findFrateries(
            $enfant,
            [$tuteur]
        );

        /*
         * Pas de fratries
         * Force 1
         */
        if ([] === $fratries) {
            return 1;
        }

        $presents = $this->getFratriesPresents($presence);

        /**
         * Pas de fratries ce jour là
         * Force premier.
         */
        $countPresents = \count($presents);
        if (0 === $countPresents) {
            return 1;
        }

        $presents[] = $enfant;
        /*
         * si pas de date naissance on force 1;
         */
        foreach ($presents as $present) {
            if (null === $present->getBirthday()) {
                return 1;
            }
        }

        $presents = SortUtils::sortByBirthday($presents);

        foreach ($presents as $key => $present) {
            if ($present->getId() === $enfant->getId()) {
                return $key + 1;
            }
        }

        //force prix plein si on a pas de date naissance
        return 1;
    }

    /**
     * @return array|Enfant[]
     */
    public function getFratriesPresents(Presence $presence): array
    {
        $tuteur = $presence->getTuteur();
        /**
         * Ordre suivant la fratries.
         */
        $fratries = $this->relationRepository->findFrateries(
            $presence->getEnfant(),
            [$tuteur]
        );

        if ([] === $fratries) {
            return [];
        }

        $jour = $presence->getJour();
        $presents = [];
        foreach ($fratries as $fratry) {
            if (null !== $this->presenceRepository->findByTuteurEnfantAndJour($tuteur, $fratry, $jour)) {
                $presents[] = $fratry;
            }
        }

        return $presents;
    }
}
