<?php

namespace AcMarche\Edr\Scolaire\Utils;

use AcMarche\Edr\Entity\Enfant;
use AcMarche\Edr\Entity\Scolaire\GroupeScolaire;
use AcMarche\Edr\Scolaire\Repository\GroupeScolaireRepository;

final readonly class ScolaireUtils
{
    public function __construct(
        private GroupeScolaireRepository $groupeScolaireRepository
    ) {
    }

    /**
     * Retourne le groupe scolaire de l'enfant
     * Si a pas retourne le groupe scolaire de son année
     * Si a pas retourne un groupe au hasard.
     */
    public function findGroupeScolaireEnfantByAnneeScolaire(Enfant $enfant): GroupeScolaire
    {
        if (($groupeScolaire = $enfant->getGroupeScolaire()) instanceof GroupeScolaire) {
            return $groupeScolaire;
        }

        $anneeScolaire = $enfant->getAnneeScolaire();

        if (($groupeScolaire = $anneeScolaire->getGroupeScolaire()) instanceof GroupeScolaire) {
            return $groupeScolaire;
        }

        $groupes = $this->groupeScolaireRepository->findGroupesNotPlaine();

        return $groupes[0];
    }

    /**
     * @param Enfant $age
     */
    public function findGroupeScolaireEnfantByAge(?float $age): ?GroupeScolaire
    {
        if (!$age) {
            return null;
        }

        return $this->groupeScolaireRepository->findGroupePlaineByAge($age);
    }
}
