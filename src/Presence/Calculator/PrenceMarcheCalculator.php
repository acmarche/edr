<?php

namespace AcMarche\Edr\Presence\Calculator;

use AcMarche\Edr\Contrat\Presence\PresenceCalculatorInterface;
use AcMarche\Edr\Contrat\Presence\PresenceInterface;
use AcMarche\Edr\Data\EdrConstantes;
use AcMarche\Edr\Entity\Facture\FacturePresence;
use AcMarche\Edr\Entity\Jour;
use AcMarche\Edr\Entity\Plaine\Plaine;
use AcMarche\Edr\Entity\Reduction;
use AcMarche\Edr\Entity\Scolaire\Ecole;
use AcMarche\Edr\Reduction\Calculator\ReductionCalculator;
use AcMarche\Edr\Relation\Utils\OrdreService;

final class PrenceMarcheCalculator implements PresenceCalculatorInterface
{
    public array $ecoles;

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

        return $this->calculatePresence($presence);
    }

    public function setMetaDatas(PresenceInterface $presence, FacturePresence $facturePresence): void
    {
        $facturePresence->setPedagogique($presence->getJour()->isPedagogique());
        $facturePresence->setPresenceDate($presence->getJour()->getDateJour());

        $enfant = $presence->getEnfant();
        if ($enfant->getEcole() instanceof Ecole) {
            $this->ecoles[] = $enfant->getEcole()->getNom();
        }

        $facturePresence->setNom($enfant->getNom());
        $facturePresence->setPrenom($enfant->getPrenom());

        $ordre = $this->getOrdreOnPresence($presence);
        $facturePresence->setOrdre($ordre);
        $facturePresence->setAbsent($presence->getAbsent());
        $facturePresence->setCoutBrut($this->getPrixByOrdre($presence, $ordre));
    }

    public function getOrdreOnPresence(PresenceInterface $presence): int
    {
        return $this->ordreService->getOrdreOnPresence($presence);
    }

    public function getPrixByOrdre(PresenceInterface $presence, int $ordre): float
    {
        $jour = $presence->getJour();
        if ($ordre >= 3) {
            return $jour->getPrix3();
        }

        if (2 === $ordre) {
            return $jour->getPrix2();
        }

        return $jour->getPrix1();
    }

    private function calculatePresence(PresenceInterface $presence): float
    {
        $ordre = $this->ordreService->getOrdreOnPresence($presence);
        $prix = $this->getPrixByOrdre($presence, $ordre);

        return $this->reductionApplicate($presence, $prix);
    }

    private function calculatePlaine(PresenceInterface $presence, Jour $jour): float
    {
        $plaine = $jour->getPlaine();
        $ordre = $this->getOrdreOnPresence($presence);

        $prix = match ($ordre) {
            2 => $plaine->getPrix2(),
            3 => $plaine->getPrix3(),
            default => $plaine->getPrix1(),
        };

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
