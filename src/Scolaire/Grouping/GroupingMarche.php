<?php

namespace AcMarche\Edr\Scolaire\Grouping;

use AcMarche\Edr\Entity\Enfant;
use AcMarche\Edr\Entity\Plaine\Plaine;
use AcMarche\Edr\Entity\Scolaire\GroupeScolaire;
use AcMarche\Edr\Scolaire\Utils\ScolaireUtils;
use AcMarche\Edr\Utils\SortUtils;

class GroupingMarche implements GroupingInterface
{
    public function __construct(
        private readonly ScolaireUtils $scolaireUtils
    ) {
    }

    public function groupEnfantsForPresence(array $enfants): array
    {
        $groups = [];
        foreach ($enfants as $enfant) {
            $groupe = $this->findGroupeScolaireByAnneeScolaire($enfant);
            $groups[$groupe->getId()]['groupe'] = $groupe;
            $groups[$groupe->getId()]['enfants'][] = $enfant;
        }

        return SortUtils::sortGroupesScolairesByOrder($groups);
    }

    /**
     * @param array|Enfant[] $enfants
     *
     * @return array|Enfant[]
     */
    public function groupEnfantsForPlaine(Plaine $plaine, array $enfants): array
    {
        $groups = [];
        $jour = $plaine->getFirstDay();
        $date = $jour->getDateJour();
        if ($plaine->getPlaineGroupes()->count() > 0) {
            $groupeForce = $plaine->getPlaineGroupes()[0]->getGroupeScolaire();
            $groupeForce->setNom('Non classé');
        } else {
            $groupeForce = new GroupeScolaire();
            $groupeForce->setNom('Inexistant');
        }

        foreach ($enfants as $enfant) {
            $groupe = $this->findGroupeScolaireByAge($enfant->getAge($date, true));
            if (!$groupe instanceof GroupeScolaire) {
                $groupe = $groupeForce;
            }

            $groups[$groupe->getId()]['groupe'] = $groupe;
            $groups[$groupe->getId()]['enfants'][] = $enfant;
        }

        return SortUtils::sortGroupesScolairesByOrder($groups);
    }

    public function findGroupeScolaireByAge(float $age): ?GroupeScolaire
    {
        return $this->scolaireUtils->findGroupeScolaireEnfantByAge($age);
    }

    public function findGroupeScolaireByAnneeScolaire(Enfant $enfant): ?GroupeScolaire
    {
        return $this->scolaireUtils->findGroupeScolaireEnfantByAnneeScolaire($enfant);
    }
}
