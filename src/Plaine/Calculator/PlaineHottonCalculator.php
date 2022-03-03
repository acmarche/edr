<?php

namespace AcMarche\Edr\Plaine\Calculator;

use AcMarche\Edr\Contrat\Plaine\PlaineCalculatorInterface;
use AcMarche\Edr\Contrat\Presence\PresenceInterface;
use AcMarche\Edr\Data\EdrConstantes;
use AcMarche\Edr\Entity\Plaine\Plaine;
use AcMarche\Edr\Reduction\Calculator\ReductionCalculator;
use AcMarche\Edr\Relation\Utils\OrdreService;

final class PlaineHottonCalculator implements PlaineCalculatorInterface
{
    public function __construct(
        private OrdreService $ordreService,
        private ReductionCalculator $reductionCalculator
    ) {
    }

    /**
     * @param array|PresenceInterface[] $presences
     */
    public function calculate(Plaine $plaine, array $presences): float
    {
        $total = 0;
        foreach ($presences as $presence) {
            $cout = $this->calculateOnePresence($plaine, $presence);
            $total += $cout;
        }

        return $total;
    }

    public function calculateOnePresence(Plaine $plaine, PresenceInterface $presence): float
    {
        if (EdrConstantes::ABSENCE_AVEC_CERTIF === $presence->getAbsent()) {
            return 0;
        }
        $ordre = $this->getOrdreOnePresence($presence);
        $prix = $this->getPrixByOrdre($plaine, $ordre);

        return $this->applicateReduction($presence, $prix);
    }

    public function getOrdreOnePresence(PresenceInterface $presence): int
    {
        return $this->ordreService->getOrdreOnPresence($presence);
    }

    public function getPrixByOrdre(Plaine $plaine, $ordre): float
    {
        if ($ordre > 1) {
            return $plaine->getPrix2();
        }

        return $plaine->getPrix1();
    }

    private function applicateReduction(PresenceInterface $presence, float $cout): float
    {
        if (null !== ($reduction = $presence->getReduction())) {
            return $this->reductionCalculator->applicate($reduction, $cout);
        }

        return $cout;
    }
}
