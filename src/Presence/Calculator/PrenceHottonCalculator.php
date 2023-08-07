<?php

namespace AcMarche\Edr\Presence\Calculator;

use AcMarche\Edr\Contrat\Presence\PresenceCalculatorInterface;
use AcMarche\Edr\Contrat\Presence\PresenceInterface;
use AcMarche\Edr\Data\EdrConstantes;
use AcMarche\Edr\Entity\Jour;
use AcMarche\Edr\Entity\Plaine\Plaine;
use AcMarche\Edr\Entity\Reduction;
use AcMarche\Edr\Reduction\Calculator\ReductionCalculator;
use AcMarche\Edr\Relation\Utils\OrdreService;

final class PrenceHottonCalculator implements PresenceCalculatorInterface
{
    public function __construct(
        private readonly OrdreService $ordreService,
        private readonly ReductionCalculator $reductionCalculator
    ) {
    }

    public function calculate(PresenceInterface $presence): float
    {
        /*
         * Absence.avec certificat
         */
        if (EdrConstantes::ABSENCE_AVEC_CERTIF === $presence->getAbsent()) {
            return 0;
        }

        $jour = $presence->getJour();

        if ($jour->getPlaine() instanceof Plaine) {
            return $this->calculatePlaine($presence, $jour);
        }

        return $this->calculatePresence($presence, $jour);
    }

    public function getPrixByOrdre(PresenceInterface $presence, $ordre): float
    {
        $jour = $presence->getJour();

        if ($jour->isPedagogique()) {
            return $presence->isHalf() ? $jour->getPrix2() : $jour->getPrix1();
        }

        if ($ordre >= 3) {
            return $jour->getPrix3();
        }

        if (2 === $ordre) {
            return $jour->getPrix2();
        }

        return $jour->getPrix1();
    }

    public function getOrdreOnPresence(PresenceInterface $presence): int
    {
        return $this->ordreService->getOrdreOnPresence($presence);
    }

    private function calculatePresence(PresenceInterface $presence, Jour $jour): float
    {
        $ordre = $this->getOrdreOnPresence($presence);
        $prix = $this->getPrixByOrdre($presence, $ordre);

        return $this->reductionApplicate($presence, $prix);
    }

    private function calculatePlaine(PresenceInterface $presence, Jour $jour): float
    {
        $plaine = $jour->getPlaine();
        $ordre = $this->getOrdreOnPresence($presence);
        $prix = $plaine->getPrix1();
        //todo !!!! prix plaine

        if ($ordre > 1) {
            $prix = $plaine->getPrix1();
        }

        return $this->reductionApplicate($presence, $prix);
    }

    private function reductionApplicate(PresenceInterface $presence, float $cout): float
    {
        if (($reduction = $presence->getReduction()) instanceof Reduction) {
            return $this->reductionCalculator->applicate($reduction, $cout);
        }

        return $cout;
    }
}
