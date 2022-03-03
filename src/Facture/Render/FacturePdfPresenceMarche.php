<?php

namespace AcMarche\Edr\Facture\Render;

use AcMarche\Edr\Contrat\Facture\FacturePdfPresenceInterface;
use AcMarche\Edr\Entity\Facture\FacturePresence;
use AcMarche\Edr\Facture\FactureInterface;
use AcMarche\Edr\Facture\Repository\FacturePresenceRepository;
use AcMarche\Edr\Facture\Utils\FactureUtils;
use AcMarche\Edr\Organisation\Repository\OrganisationRepository;
use Twig\Environment;

class FacturePdfPresenceMarche implements FacturePdfPresenceInterface
{
    public function __construct(
        private Environment $environment,
        private OrganisationRepository $organisationRepository,
        private FactureUtils $factureUtils,
        private FacturePresenceRepository $facturePresenceRepository
    ) {
    }

    public function render(FactureInterface $facture): string
    {
        $content = $this->prepareContent($facture);

        return $this->environment->render(
            '@AcMarcheEdrAdmin/facture/marche/pdf.html.twig',
            [
                'content' => $content,
            ]
        );
    }

    public function renderMultiple(array $factures): string
    {
        $content = '';
        foreach ($factures as $facture) {
            $content .= $this->prepareContent($facture);
            $content .= '<div class="page-breaker"></div>';
        }

        return $this->environment->render(
            '@AcMarcheEdrAdmin/facture/marche/pdf.html.twig',
            [
                'content' => $content,
            ]
        );
    }

    private function prepareContent(FactureInterface $facture): string
    {
        $organisation = $this->organisationRepository->getOrganisation();
        $data = [
            'enfants' => [],
            'cout' => 0,
        ];
        //init
        foreach ($this->factureUtils->getEnfants($facture) as $slug => $enfant) {
            $data['enfants'][$slug] = [
                'enfant' => $enfant,
                'cout' => 0,
                'edr' => 0,
            ];
        }

        $tuteur = $facture->getTuteur();
        $facturePresences = $this->facturePresenceRepository->findByFactureAndType(
            $facture,
            FactureInterface::OBJECT_PRESENCE
        );

        foreach ($facturePresences as $facturePresence) {
            $data = $this->groupPresences($facturePresence, $data);
        }

        foreach ($data['enfants'] as $enfant) {
            $data['cout'] += $enfant['cout'];
        }

        return $this->environment->render(
            '@AcMarcheEdrAdmin/facture/marche/_presence_content_pdf.html.twig',
            [
                'facture' => $facture,
                'tuteur' => $tuteur,
                'organisation' => $organisation,
                'data' => $data,
                'countPresences' => \count($facturePresences),
            ]
        );
    }

    private function groupPresences(FacturePresence $facturePresence, array $data): array
    {
        $enfant = $facturePresence->getNom().' '.$facturePresence->getPrenom();
        $slug = $this->factureUtils->slugger->slug($enfant);
        if (! $facturePresence->isPedagogique()) {
            ++$data['enfants'][$slug->toString()]['edr'];
        }
        $data['enfants'][$slug->toString()]['cout'] += $facturePresence->getCoutCalculated();

        return $data;
    }
}
