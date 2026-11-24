<?php

namespace AcMarche\Edr\Entity\Traits;

use AcMarche\Edr\Entity\Scolaire\GroupeScolaire;

trait GroupeScolaireTrait
{
    private ?GroupeScolaire $groupe_scolaire = null;

    public function getGroupeScolaire(): ?GroupeScolaire
    {
        return $this->groupe_scolaire;
    }

    public function setGroupeScolaire(?GroupeScolaire $groupe_scolaire): self
    {
        $this->groupe_scolaire = $groupe_scolaire;

        return $this;
    }
}
