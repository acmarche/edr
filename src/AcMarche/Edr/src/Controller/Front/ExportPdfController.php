<?php

namespace AcMarche\Edr\Controller\Front;

use AcMarche\Edr\Entity\Enfant;
use AcMarche\Edr\Sante\Factory\SantePdfFactoryTrait;
use AcMarche\Edr\Sante\Handler\SanteHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/export/pdf')]
final class ExportPdfController extends AbstractController
{
    public function __construct(
        private readonly SanteHandler $santeHandler,
        private readonly SantePdfFactoryTrait $santePdfFactory
    ) {
    }

    #[Route(path: '/santefiche/{uuid}', name: 'edr_commun_export_sante_fiche_pdf')]
    #[IsGranted('enfant_show', subject: 'enfant')]
    public function sante(Enfant $enfant): Response
    {
        $santeFiche = $this->santeHandler->init($enfant);

        return $this->santePdfFactory->santeFiche($santeFiche);
    }

}
