<?php

namespace AcMarche\Edr\Entity\Traits;

use AcMarche\Edr\Entity\Tuteur;

trait TuteurTrait
{
    private ?Tuteur $tuteur = null;

    public function getTuteur(): ?Tuteur
    {
        return $this->tuteur;
    }

    public function setTuteur(?Tuteur $tuteur): void
    {
        $this->tuteur = $tuteur;
    }
}
