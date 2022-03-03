<?php

namespace AcMarche\Edr\Entity\Plaine;

use Doctrine\ORM\Mapping as ORM;

trait InscriptionOpenTrait
{
    #[ORM\Column(type: 'boolean')]
    private bool $inscriptionOpen = false;

    public function isInscriptionOpen(): bool
    {
        return $this->inscriptionOpen;
    }

    public function setInscriptionOpen(bool $inscriptionOpen): void
    {
        $this->inscriptionOpen = $inscriptionOpen;
    }
}
