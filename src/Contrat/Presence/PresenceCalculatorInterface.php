<?php

namespace AcMarche\Edr\Contrat\Presence;

interface PresenceCalculatorInterface
{
    public function calculate(PresenceInterface $presence): float;

    public function getOrdreOnPresence(PresenceInterface $presence): int;

    public function getPrixByOrdre(PresenceInterface $presence, int $ordre): float;
}
