<?php

namespace AcMarche\Edr\Controller\Front;

use AcMarche\Edr\Entity\Enfant;
use AcMarche\Edr\Entity\Facture\Creance;
use AcMarche\Edr\Entity\Facture\Facture;
use AcMarche\Edr\Facture\Factory\FacturePdfFactoryTrait;
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
        private readonly SantePdfFactoryTrait $santePdfFactory,
        private readonly FacturePdfFactoryTrait $facturePdfFactory
    ) {
    }

    #[Route(path: '/santefiche/{uuid}', name: 'edr_commun_export_sante_fiche_pdf')]
    #[IsGranted('enfant_show', subject: 'enfant')]
    public function sante(Enfant $enfant): Response
    {
        $santeFiche = $this->santeHandler->init($enfant);

        return $this->santePdfFactory->santeFiche($santeFiche);
    }

    #[Route(path: '/facture/{uuid}', name: 'edr_commun_export_facture_pdf')]
    public function facture(Facture $facture): Response
    {
        $tuteur = $facture->getTuteur();
        $this->denyAccessUnlessGranted('tuteur_show', $tuteur);

        return $this->facturePdfFactory->generate($facture);
    }

    #[Route(path: '/creance/{uuid}', name: 'edr_commun_export_creance_pdf')]
    public function creance(Creance $creance): Response
    {
        return new Response('todo');
        $tuteur = $creance->getTuteur();
        $this->denyAccessUnlessGranted('tuteur_show', $tuteur);

        return $this->facturePdfFactory->generate($creance);
    }
}
