<?php

namespace AcMarche\Edr\Facture\Repository;

use AcMarche\Edr\Accueil\Repository\AccueilRepository;
use AcMarche\Edr\Entity\Presence\Accueil;
use AcMarche\Edr\Entity\Presence\Presence;
use AcMarche\Edr\Entity\Tuteur;
use AcMarche\Edr\Facture\FactureInterface;
use AcMarche\Edr\Presence\Repository\PresenceRepository;
use DateTimeInterface;

class FacturePresenceNonPayeRepository
{
    public function __construct(
        private readonly PresenceRepository $presenceRepository,
        private readonly AccueilRepository $accueilRepository,
        private readonly FacturePresenceRepository $facturePresenceRepository
    ) {
    }

    /**
     * @return array|Presence[]
     */
    public function findPresencesNonPayes(Tuteur $tuteur, ?DateTimeInterface $date = null): array
    {
        $presences = $this->presenceRepository->findByTuteurAndMonth($tuteur, $date);
        $ids = array_map(
            static fn($presence) => $presence->getId(),
            $presences
        );
        $presencesPayes = $this->facturePresenceRepository->findByIdsAndType($ids, FactureInterface::OBJECT_PRESENCE);
        $idPayes = array_map(
            static fn($presence) => $presence->getPresenceId(),
            $presencesPayes
        );
        $idsNonPayes = array_diff($ids, $idPayes);

        return $this->presenceRepository->findBy([
            'id' => $idsNonPayes,
        ]);
    }

    /**
     * @return array|Accueil[]
     */
    public function findAccueilsNonPayes(Tuteur $tuteur, ?DateTimeInterface $date = null): array
    {
        $accueils = $this->accueilRepository->findByTuteurAndMonth($tuteur, $date);
        $ids = array_map(
            static fn($accueil) => $accueil->getId(),
            $accueils
        );
        $presencesPayes = $this->facturePresenceRepository->findByIdsAndType($ids, FactureInterface::OBJECT_ACCUEIL);
        $idPayes = array_map(
            static fn($presence) => $presence->getPresenceId(),
            $presencesPayes
        );
        $idsNonPayes = array_diff($ids, $idPayes);

        return $this->accueilRepository->findBy([
            'id' => $idsNonPayes,
        ]);
    }
}
