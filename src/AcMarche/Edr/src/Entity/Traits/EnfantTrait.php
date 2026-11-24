<?php

namespace AcMarche\Edr\Entity\Traits;

use AcMarche\Edr\Entity\Enfant;

trait EnfantTrait
{
    private ?Enfant $enfant = null;

    public function getEnfant(): ?Enfant
    {
        return $this->enfant;
    }

    public function setEnfant(?Enfant $enfant): void
    {
        $this->enfant = $enfant;
    }
}
