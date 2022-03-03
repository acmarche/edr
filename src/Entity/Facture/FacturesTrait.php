<?php

namespace AcMarche\Edr\Entity\Facture;

use Doctrine\Common\Collections\Collection;

trait FacturesTrait
{
    /**
     * @var Facture[]
     */
    private Collection $factures;

    /**
     * @return Facture[]
     */
    public function getFactures(): array
    {
        return $this->factures;
    }

    /**
     * @param Facture[] $factures
     */
    public function setFactures(Collection $factures): void
    {
        $this->factures = $factures;
    }
}
