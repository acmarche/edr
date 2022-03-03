<?php

namespace AcMarche\Edr\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait PapierTrait
{
    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $facture_papier = false;

    public function getFacturePapier(): ?bool
    {
        return $this->facture_papier;
    }

    public function setFacturePapier(bool $papier): void
    {
        $this->facture_papier = $papier;
    }
}
