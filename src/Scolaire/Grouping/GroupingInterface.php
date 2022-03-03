<?php

namespace AcMarche\Edr\Scolaire\Grouping;

use AcMarche\Edr\Entity\Enfant;
use AcMarche\Edr\Entity\Plaine\Plaine;
use AcMarche\Edr\Entity\Scolaire\GroupeScolaire;

interface GroupingInterface
{
    public function findGroupeScolaireByAge(float $age): ?GroupeScolaire;

    public function findGroupeScolaireByAnneeScolaire(Enfant $enfant): ?GroupeScolaire;

    public function groupEnfantsForPresence(array $enfants): array;

    public function groupEnfantsForPlaine(Plaine $plaine, array $enfants): array;
}
