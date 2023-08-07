<?php

namespace AcMarche\Edr\Controller\Admin;

use AcMarche\Edr\Facture\Repository\FactureRepository;
use AcMarche\Edr\Facture\Utils\FactureUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route(path: '/attestation')]
#[IsGranted(data: 'ROLE_MERCREDI_ADMIN')]
final class AttestationController extends AbstractController
{
    public function __construct(
        private readonly FactureRepository $factureRepository,
        private readonly FactureUtils $factureUtils
    ) {
    }

    #[Route(path: '/', name: 'edr_admin_attestation')]
    public function default(): Response
    {
        $year = 2021;
        $factures = $this->factureRepository->findFacturesPaid($year);
        $total = count($factures);
        $data = $this->factureUtils->groupByTuteur($factures);

        return $this->render(
            '@AcMarcheEdr/admin/attestation/index.html.Twig',
            [
                'data' => $data,
                'year' => $year,
                'total' => $total,
            ]
        );
    }
}
