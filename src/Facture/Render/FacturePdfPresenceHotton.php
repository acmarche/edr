<?php

namespace AcMarche\Edr\Facture\Render;

use AcMarche\Edr\Contrat\Facture\FacturePdfPresenceInterface;
use AcMarche\Edr\Entity\Facture\FacturePresence;
use AcMarche\Edr\Facture\FactureInterface;
use AcMarche\Edr\Facture\Repository\FacturePresenceRepository;
use AcMarche\Edr\Facture\Utils\FactureUtils;
use AcMarche\Edr\Organisation\Repository\OrganisationRepository;
use Twig\Environment;

class FacturePdfPresenceHotton implements FacturePdfPresenceInterface
{
    public function __construct(
        private readonly Environment $environment,
        private readonly OrganisationRepository $organisationRepository,
        private readonly FactureUtils $factureUtils,
        private readonly FacturePresenceRepository $facturePresenceRepository
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

    public function renderMultiple(array $factures): string
    {
        $content = '';
        foreach ($factures as $facture) {
            $content .= $this->prepareContent($facture);
            $content .= '<div class="page-breaker"></div>';
        }

        return $this->environment->render(
            '@AcMarcheEdrAdmin/facture/hotton/pdf.html.twig',
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
                'peda' => 0,
                'edr' => 0,
                'accueils' => [
                    'Soir' => [
                        'nb' => 0,
                        'cout' => 0,
                    ],
                    'Matin' => [
                        'nb' => 0,
                        'cout' => 0,
                        
                    ],
                ],
            ];
        }

        $tuteur = $facture->getTuteur();
        $facturePresences = $this->facturePresenceRepository->findByFactureAndType(
            $facture,
            FactureInterface::OBJECT_PRESENCE
        );
        $factureAccueils = $this->facturePresenceRepository->findByFactureAndType(
            $facture,
            FactureInterface::OBJECT_ACCUEIL
        );

        foreach ($facturePresences as $facturePresence) {
            $data = $this->groupPresences($facturePresence, $data);
        }

        foreach ($factureAccueils as $factureAccueil) {
            $data = $this->groupAccueils($factureAccueil, $data);
        }

        foreach ($data['enfants'] as $enfant) {
            $data['cout'] += $enfant['cout'];
        }

        return $this->environment->render(
            '@AcMarcheEdrAdmin/facture/hotton/_presence_content_pdf.html.twig',
            [
                'facture' => $facture,
                'tuteur' => $tuteur,
                'organisation' => $organisation,
                'data' => $data,
                'countAccueils' => \count($factureAccueils),
                'countPresences' => \count($facturePresences),
            ]
        );
    }

    private function groupAccueils(FacturePresence $facturePresence, array $data): array
    {
        $enfant = $facturePresence->getNom() . ' ' . $facturePresence->getPrenom();
        $slug = $this->factureUtils->slugger->slug($enfant);
        $heure = $facturePresence->getHeure();
        $duree = $facturePresence->getDuree();
        $data['enfants'][$slug->toString()]['cout'] += $facturePresence->getCoutCalculated();
        $data['enfants'][$slug->toString()]['accueils'][$heure]['nb'] += $duree;

        return $data;
    }

    private function groupPresences(FacturePresence $facturePresence, array $data): array
    {
        $enfant = $facturePresence->getNom() . ' ' . $facturePresence->getPrenom();
        $slug = $this->factureUtils->slugger->slug($enfant);
        if ($facturePresence->isPedagogique()) {
            ++$data['enfants'][$slug->toString()]['peda'];
        }

        if (!$facturePresence->isPedagogique()) {
            ++$data['enfants'][$slug->toString()]['edr'];
        }

        $data['enfants'][$slug->toString()]['cout'] += $facturePresence->getCoutCalculated();

        return $data;
    }
}
