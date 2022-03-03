<?php

namespace AcMarche\Edr\Entity\Security\Traits;

trait IsRoleTrait
{
    public function isParent(): bool
    {
        return (bool) \in_array('ROLE_MERCREDI_PARENT', $this->getRoles(), true);
    }

    public function isAnimateur(): bool
    {
        return (bool) \in_array('ROLE_MERCREDI_ANIMATEUR', $this->getRoles(), true);
    }

    public function isEcole(): bool
    {
        return (bool) \in_array('ROLE_MERCREDI_ECOLE', $this->getRoles(), true);
    }
}
