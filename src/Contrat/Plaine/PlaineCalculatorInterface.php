<?php

namespace AcMarche\Edr\Contrat\Plaine;

use AcMarche\Edr\Contrat\Presence\PresenceInterface;
use AcMarche\Edr\Entity\Plaine\Plaine;

interface PlaineCalculatorInterface
{
    public function calculate(Plaine $plaine, array $presences): float;

    public function calculateOnePresence(Plaine $plaine, PresenceInterface $presence): float;

    public function getOrdreOnePresence(PresenceInterface $presence): int;

    public function getPrixByOrdre(Plaine $plaine, $ordre): float;
}
