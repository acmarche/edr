<?php

namespace AcMarche\Edr\Facture\Render;

use AcMarche\Edr\Contrat\Facture\FactureCalculatorInterface;
use AcMarche\Edr\Contrat\Facture\FactureRenderInterface;
use AcMarche\Edr\Facture\FactureInterface;
use AcMarche\Edr\Facture\Repository\FactureComplementRepository;
use AcMarche\Edr\Facture\Repository\FactureDecompteRepository;
use AcMarche\Edr\Facture\Repository\FacturePresenceRepository;
use AcMarche\Edr\Facture\Repository\FactureReductionRepository;
use Twig\Environment;

class FactureRenderHotton implements FactureRenderInterface
{
    public function __construct(
        private readonly FacturePresenceRepository $facturePresenceRepository,
        private readonly FactureReductionRepository $factureReductionRepository,
        private readonly FactureComplementRepository $factureComplementRepository,
        private readonly FactureCalculatorInterface $factureCalculator,
        private readonly FactureDecompteRepository $factureDecompteRepository,
        private readonly Environment $environment
    ) {
    }

    public function render(FactureInterface $facture): string
    {
        if ($facture->getPlaineNom()) {
            return $this->renderForPlaine($facture);
        }

        return $this->renderForPresence($facture);
    }

    public function renderForPresence(FactureInterface $facture): string
    {
        $tuteur = $facture->getTuteur();
        $facturePresences = $this->facturePresenceRepository->findByFactureAndType(
            $facture,
            FactureInterface::OBJECT_PRESENCE
        );
        $factureAccueils = $this->facturePresenceRepository->findByFactureAndType(
            $facture,
            FactureInterface::OBJECT_ACCUEIL
        );

        $factureReductions = $this->factureReductionRepository->findByFacture($facture);
        $factureComplements = $this->factureComplementRepository->findByFacture($facture);
        $factureDecomptes = $this->factureDecompteRepository->findByFacture($facture);

        $dto = $this->factureCalculator->createDetail($facture);

        return $this->environment->render(
            '@AcMarcheEdrAdmin/facture/hotton/_show_presence.html.twig',
            [
                'facture' => $facture,
                'tuteur' => $tuteur,
                'facturePresences' => $facturePresences,
                'factureAccueils' => $factureAccueils,
                'factureReductions' => $factureReductions,
                'factureComplements' => $factureComplements,
                'factureDecomptes' => $factureDecomptes,
                'dto' => $dto,
            ]
        );
    }

    public function renderForPlaine(FactureInterface $facture): string
    {
        $tuteur = $facture->getTuteur();
        $facturePlaines = $this->facturePresenceRepository->findByFactureAndType(
            $facture,
            FactureInterface::OBJECT_PLAINE
        );
        $dto = $this->factureCalculator->createDetail($facture);

        return $this->environment->render(
            '@AcMarcheEdrAdmin/facture/hotton/_show_plaine.html.twig',
            [
                'facture' => $facture,
                'tuteur' => $tuteur,
                'facturePlaines' => $facturePlaines,
                'dto' => $dto,
            ]
        );
    }
}
