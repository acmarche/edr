<?php

namespace AcMarche\Edr\Controller\Parent;

use AcMarche\Edr\Entity\Enfant;
use AcMarche\Edr\Pdf\PdfDownloaderTrait;
use AcMarche\Edr\Relation\Repository\RelationRepository;
use AcMarche\Edr\Relation\Utils\RelationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/attestation')]
final class AttestationController extends AbstractController
{
    use GetTuteurTrait;
    use PdfDownloaderTrait;

    public function __construct(
        public RelationRepository $relationRepository
    ) {
    }

    #[Route(path: '/{year}/{uuid}', name: 'edr_parent_attestation')]
    #[IsGranted(data: 'enfant_show', subject: 'enfant')]
    public function default(int $year, Enfant $enfant): Response
    {
        if (($hasTuteur = $this->hasTuteur()) instanceof Response) {
            return $hasTuteur;
        }

        $relations = $this->relationRepository->findByTuteur($this->tuteur);
        $enfants = RelationUtils::extractEnfants($relations);
        $factures = [];
        $html = $this->renderView(
            '@AcMarcheEdr/commun/attestation/index.html.Twig',
            [
                'enfants' => $enfants,
                'factures' => $factures,
                'tuteur' => $this->tuteur,
                'year' => $year,
            ]
        );

        return $this->downloadPdf($html, $enfant->getSlug() . '-attestation-' . $year . '.pdf');
    }
}
