<?php

namespace AcMarche\Edr\Entity\Traits;

use AcMarche\Edr\Entity\Paiement;
use Doctrine\ORM\Mapping as ORM;

trait PaiementTrait
{
    #[ORM\ManyToOne(targetEntity: Paiement::class)]
    private ?Paiement $paiement = null;

    public function getPaiement(): ?Paiement
    {
        return $this->paiement;
    }

    public function setPaiement(?Paiement $paiement): void
    {
        $this->paiement = $paiement;
    }
}
