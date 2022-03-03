<?php

namespace AcMarche\Edr\Entity\Traits;

trait PhotoAutorisationTrait
{
    private bool $photo_autorisation;

    public function isPhotoAutorisation(): bool
    {
        return $this->photo_autorisation;
    }

    public function setPhotoAutorisation(bool $photo_autorisation): void
    {
        $this->photo_autorisation = $photo_autorisation;
    }

    public function getPhotoAutorisation(): ?bool
    {
        return $this->photo_autorisation;
    }
}
