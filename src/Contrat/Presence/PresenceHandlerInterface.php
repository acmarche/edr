<?php

namespace AcMarche\Edr\Contrat\Presence;

use AcMarche\Edr\Entity\Enfant;
use AcMarche\Edr\Entity\Jour;
use AcMarche\Edr\Entity\Scolaire\Ecole;
use AcMarche\Edr\Entity\Tuteur;
use Doctrine\ORM\NonUniqueResultException;

interface PresenceHandlerInterface
{
    /**
     * @param Jour[] $days
     *
     * @throws NonUniqueResultException
     */
    public function handleNew(Tuteur $tuteur, Enfant $enfant, iterable $days): void;

    public function searchAndGrouping(Jour $jour, ?Ecole $ecole, bool $displayRemarque): array;

    public function checkConstraints(Jour $jour): bool;
}
