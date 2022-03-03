<?php

namespace AcMarche\Edr\Facture\Factory;

use AcMarche\Edr\Entity\Plaine\Plaine;
use AcMarche\Edr\Facture\FactureInterface;

interface CommunicationFactoryInterface
{
    public function generateForPresence(FactureInterface $facture): string;

    public function generateForPlaine(Plaine $plaine, FactureInterface $facture): string;
}
