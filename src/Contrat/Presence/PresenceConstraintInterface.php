<?php

namespace AcMarche\Edr\Contrat\Presence;

use AcMarche\Edr\Entity\Jour;

interface PresenceConstraintInterface
{
    public function addFlashError(Jour $jour);

    public function check(Jour $jour): bool;
}
