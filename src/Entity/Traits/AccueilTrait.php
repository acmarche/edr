<?php

namespace AcMarche\Edr\Entity\Traits;

use AcMarche\Edr\Entity\Presence\Accueil;

trait AccueilTrait
{
    private ?Accueil $accueil = null;

    public function getAccueil(): ?Accueil
    {
        return $this->accueil;
    }

    public function setAccueil(?Accueil $accueil): void
    {
        $this->accueil = $accueil;
    }
}
