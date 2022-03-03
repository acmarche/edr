<?php

namespace AcMarche\Edr\Contrat\Facture;

use AcMarche\Edr\Facture\Dto\FactureDetailDto;
use AcMarche\Edr\Facture\FactureInterface;

interface FactureCalculatorInterface
{
    public function createDetail(FactureInterface $facture): FactureDetailDto;

    /**
     * Total de la facture, complements et reductions compris.
     */
    public function total(FactureInterface $facture): float;

    /**
     * Total.
     */
    public function totalPresences(FactureInterface $facture): float;

    /**
     * Total.
     */
    public function totalAccueils(FactureInterface $facture): float;

    /**
     * Montant total des forfaits.
     */
    public function totalReductionForfaits(FactureInterface $facture): float;

    /**
     * Total des pourcentages a appliquer sur le total de la facture.
     */
    public function totalReductionPourcentage(FactureInterface $facture): float;

    /**
     * Montant total des forfaits.
     */
    public function totalComplementForfaits(FactureInterface $facture): float;

    /**
     * Total des pourcentages a appliquer sur le total de la facture.
     */
    public function totalComplementPourcentage(FactureInterface $facture): float;
}
