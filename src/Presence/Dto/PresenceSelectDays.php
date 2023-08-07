<?php

namespace AcMarche\Edr\Presence\Dto;

use AcMarche\Edr\Entity\Enfant;
use AcMarche\Edr\Entity\Traits\EnfantTrait;

final class PresenceSelectDays
{
    use EnfantTrait;

    protected array $jours = [];

    public function __construct(Enfant $enfant)
    {
        $this->enfant = $enfant;
    }

    public function getJours(): array
    {
        return $this->jours;
    }

    public function setJours(array $jours): void
    {
        $this->jours = $jours;
    }
}
