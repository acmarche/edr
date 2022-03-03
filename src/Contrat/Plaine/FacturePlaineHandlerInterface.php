<?php

namespace AcMarche\Edr\Contrat\Plaine;

use AcMarche\Edr\Entity\Plaine\Plaine;
use AcMarche\Edr\Entity\Tuteur;
use AcMarche\Edr\Facture\FactureInterface;

interface FacturePlaineHandlerInterface
{
    /**
     * set mois, nom plaine, plaine.
     */
    public function newInstance(Plaine $plaine, Tuteur $tuteur): FactureInterface;

    public function handleManually(FactureInterface $facture, Plaine $plaine): FactureInterface;
}
