<?php

namespace AcMarche\Edr\Facture\Render;

use AcMarche\Edr\Contrat\Facture\FactureCalculatorInterface;
use AcMarche\Edr\Contrat\Facture\FacturePdfPlaineInterface;
use AcMarche\Edr\Facture\FactureInterface;
use AcMarche\Edr\Organisation\Repository\OrganisationRepository;
use AcMarche\Edr\Plaine\Repository\PlainePresenceRepository;
use Twig\Environment;

class FacturePdfPlaineHotton implements FacturePdfPlaineInterface
{
    public function __construct(
        private OrganisationRepository $organisationRepository,
        private FactureCalculatorInterface $factureCalculator,
        private PlainePresenceRepository $plainePresenceRepository,
        private Environment $environment
    ) {
    }

    public function render(FactureInterface $facture): string
    {
        $content = $this->prepareContent($facture);

        return $this->environment->render(
            '@AcMarcheEdrAdmin/facture/hotton/pdf.html.twig',
            [
                'content' => $content,
            ]
        );
    }

    private function prepareContent(FactureInterface $facture): string
    {
        $plaine = $facture->getPlaine();
        $tuteur = $facture->getTuteur();
        $organisation = $this->organisationRepository->getOrganisation();

        $dto = $this->factureCalculator->createDetail($facture);
        $inscriptions = $this->plainePresenceRepository->findByPlaineAndTuteur($plaine, $tuteur);
        $enfants = $this->plainePresenceRepository->findEnfantsByPlaineAndTuteur($plaine, $tuteur);

        return $this->environment->render(
            '@AcMarcheEdrAdmin/facture/hotton/_plaine_content_pdf.html.twig',
            [
                'facture' => $facture,
                'tuteur' => $tuteur,
                'enfants' => $enfants,
                'inscriptions' => $inscriptions,
                'organisation' => $organisation,
                'dto' => $dto,
                'plaine' => $plaine,
            ]
        );
    }
}
