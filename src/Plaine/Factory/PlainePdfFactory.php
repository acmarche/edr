<?php

namespace AcMarche\Edr\Plaine\Factory;

use AcMarche\Edr\Entity\Scolaire\GroupeScolaire;
use AcMarche\Edr\Entity\Plaine\Plaine;
use AcMarche\Edr\Pdf\PdfDownloaderTrait;
use AcMarche\Edr\Plaine\Repository\PlainePresenceRepository;
use AcMarche\Edr\Presence\Repository\PresenceRepository;
use AcMarche\Edr\Scolaire\Grouping\GroupingInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class PlainePdfFactory
{
    use PdfDownloaderTrait;

    public function __construct(
        private readonly GroupingInterface $grouping,
        private readonly PresenceRepository $presenceRepository,
        private readonly PlainePresenceRepository $plainePresenceRepository,
        private readonly ParameterBagInterface $parameterBag,
        private readonly Environment $environment
    ) {
    }

    public function generate(Plaine $plaine): Response
    {
        $images = $this->getImagesBase64();
        $dates = $plaine->getJours();
        $firstDay = $plaine->getFirstDay()->getDateJour();

        $presences = $this->plainePresenceRepository->findByPlaine($plaine);
        /**
         * par enfant je dois avoir quel tuteur en garde
         * jours presents.
         */
        $data = [];
        $dataEnfants = [];
        $groupeForce = $plaine->getPlaineGroupes()[0]->getGroupeScolaire();
        $groupeForce->setNom('Non classé');

        $stats = [];
        foreach ($plaine->getPlaineGroupes() as $plaineGroupe) {
            foreach ($dates as $date) {
                $stats[$plaineGroupe->getGroupeScolaire()->getId()][$date->getId()]['total'] = 0;
                $stats[$plaineGroupe->getGroupeScolaire()->getId()][$date->getId()]['moins6'] = 0;
            }
        }

        foreach ($presences as $presence) {
            $enfant = $presence->getEnfant();
            $tuteur = $presence->getTuteur();
            $jour = $presence->getJour();
            $enfantId = $enfant->getId();
            $age = $enfant->getAge($firstDay, true);
            $groupeScolaire = $this->grouping->findGroupeScolaireByAge($age);
            if (!$groupeScolaire instanceof GroupeScolaire) {
                $groupeScolaire = $groupeForce;
            }

            ++$stats[$groupeScolaire->getId()][$jour->getId()]['total'];
            if ($age < 6) {
                ++$stats[$groupeScolaire->getId()][$jour->getId()]['moins6'];
            }

            $dataEnfants[$enfantId]['enfant'] = $enfant;
            $dataEnfants[$enfantId]['tuteur'] = $tuteur;
            $dataEnfants[$enfantId]['jours'][] = $jour;
            $data[$groupeScolaire->getId()]['groupe'] = $groupeScolaire;
            $data[$groupeScolaire->getId()]['enfants'] = $dataEnfants;
            $data[$groupeScolaire->getId()]['stats'] = $stats;
        }

        $html = $this->environment->render(
            '@AcMarcheEdrAdmin/plaine/pdf/plaine_pdf.html.twig',
            [
                'plaine' => $plaine,
                'firstDay' => $firstDay,
                'dates' => $dates,
                'datas' => $data,
                'stats' => $stats,
                'images' => $images,
            ]
        );

        //  return new Response($html);
        $name = $plaine->getSlug();

        $this->pdf->setOption('footer-right', '[page]/[toPage]');
        if (\count($dates) > 6) {
            $this->pdf->setOption('orientation', 'landscape');
        }

        return $this->downloadPdf($html, $name.'.pdf');
    }

    private function getImagesBase64(): array
    {
        $root = $this->parameterBag->get('kernel.project_dir').'/public/bundles/acmarcheedr/images/';
        $ok = $root.'check_ok.jpg';
        $ko = $root.'check_ko.jpg';
        $data = [];
        $data['ok'] = base64_encode(file_get_contents($ok));
        $data['ko'] = base64_encode(file_get_contents($ko));

        return $data;
    }
}
