<?php

namespace AcMarche\Edr\Contrat\Facture;

use AcMarche\Edr\Facture\FactureInterface;

interface FacturePdfPlaineInterface
{
    public function render(FactureInterface $facture): string;
}
