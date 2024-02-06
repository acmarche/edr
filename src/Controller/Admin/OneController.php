<?php

namespace AcMarche\Edr\Controller\Admin;

use AcMarche\Edr\Accueil\Repository\AccueilRepository;
use AcMarche\Edr\Pdf\PdfDownloaderTrait;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/one')]
#[IsGranted('ROLE_MERCREDI_ADMIN')]
final class OneController extends AbstractController
{
    use PdfDownloaderTrait;

    public function __construct(
        private readonly AccueilRepository $accueilRepository
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
