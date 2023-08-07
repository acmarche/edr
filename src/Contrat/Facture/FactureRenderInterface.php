<?php

namespace AcMarche\Edr\Contrat\Facture;

use AcMarche\Edr\Facture\FactureInterface;

interface FactureRenderInterface
{
    /**
     * Render html details to show the facture
     * @return string
     */
    public function render(FactureInterface $facture): string;
}
