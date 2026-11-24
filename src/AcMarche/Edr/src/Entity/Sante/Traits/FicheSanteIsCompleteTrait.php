<?php

namespace AcMarche\Edr\Entity\Sante\Traits;

trait FicheSanteIsCompleteTrait
{
    private bool $ficheSanteIsComplete = false;

    public function isFicheSanteIsComplete(): bool
    {
        return $this->ficheSanteIsComplete;
    }

    public function setFicheSanteIsComplete(bool $ficheSanteIsComplete): void
    {
        $this->ficheSanteIsComplete = $ficheSanteIsComplete;
    }
}
