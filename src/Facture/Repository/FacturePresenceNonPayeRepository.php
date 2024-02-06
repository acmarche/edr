<?php

namespace AcMarche\Edr\Facture\Repository;



use AcMarche\Edr\Entity\Presence\Presence;
use AcMarche\Edr\Entity\Tuteur;
use AcMarche\Edr\Facture\FactureInterface;
use AcMarche\Edr\Presence\Repository\PresenceRepository;
use DateTimeInterface;

class FacturePresenceNonPayeRepository
{
    public function __construct(
        private readonly PresenceRepository $presenceRepository,
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
            static fn ($presence) => $presence->getId(),
            $presences
        );
        $presencesPayes = $this->facturePresenceRepository->findByIdsAndType($ids, FactureInterface::OBJECT_PRESENCE);
        $idPayes = array_map(
            static fn ($presence) => $presence->getPresenceId(),
            $presencesPayes
        );
        $idsNonPayes = array_diff($ids, $idPayes);

        return $this->presenceRepository->findBy([
            'id' => $idsNonPayes,
        ]);
    }
}
