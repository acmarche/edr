<?php

namespace AcMarche\Edr\Entity\Facture;

use AcMarche\Edr\Facture\FactureInterface;

trait FactureTrait
{
    private FactureInterface $facture;

    public function getFacture(): FactureInterface
    {
        return $this->facture;
    }

    public function setFacture(FactureInterface $facture): void
    {
        $this->facture = $facture;
    }
}
