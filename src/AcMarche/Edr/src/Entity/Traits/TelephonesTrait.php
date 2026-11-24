<?php

namespace AcMarche\Edr\Entity\Traits;

trait TelephonesTrait
{
    private ?string $telephones = null;

    public function getTelephones(): ?string
    {
        return $this->telephones;
    }

    /**
     * @param string $telephones
     */
    public function setTelephones(?string $telephones): void
    {
        $this->telephones = $telephones;
    }
}
