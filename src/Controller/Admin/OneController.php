<?php

namespace AcMarche\Edr\Controller\Admin;

use AcMarche\Edr\Accueil\Repository\AccueilRepository;
use AcMarche\Edr\Pdf\PdfDownloaderTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route(path: '/one')]
#[IsGranted(data: 'ROLE_MERCREDI_ADMIN')]
final class OneController extends AbstractController
{
    use PdfDownloaderTrait;

    public function __construct(
        private AccueilRepository $accueilRepository
    ) {
    }

    #[Route(path: '/', name: 'edr_admin_one')]
    public function default(): Response
    {
        return $this->render(
            '@AcMarcheEdr/admin/one/index.html.Twig',
            [
            ]
        );
    }
}
