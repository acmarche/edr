<?php

namespace AcMarche\Edr\Controller\Parent;

use AcMarche\Edr\Contrat\Facture\FactureCalculatorInterface;
use AcMarche\Edr\Contrat\Facture\FactureRenderInterface;
use AcMarche\Edr\Entity\Facture\Facture;
use AcMarche\Edr\Facture\Repository\FacturePresenceRepository;
use AcMarche\Edr\Facture\Repository\FactureRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted(data: 'ROLE_MERCREDI_PARENT')]
#[Route(path: '/facture')]
final class FactureController extends AbstractController
{
    use GetTuteurTrait;

    public function __construct(
        private readonly FactureRepository $factureRepository,
        private readonly FacturePresenceRepository $facturePresenceRepository,
        private readonly FactureCalculatorInterface $factureCalculator,
        private readonly factureRenderInterface $factureRender
    ) {
    }

    #[Route(path: '/', name: 'edr_parent_facture_index', methods: ['GET', 'POST'])]
    public function index(): Response
    {
        if (($hasTuteur = $this->hasTuteur()) instanceof Response) {
            return $hasTuteur;
        }

        $factures = $this->factureRepository->findFacturesByTuteurWhoIsSend($this->tuteur);

        return $this->render(
            '@AcMarcheEdrParent/facture/index.html.twig',
            [
                'factures' => $factures,
                'tuteur' => $this->tuteur,
            ]
        );
    }

    #[Route(path: '/{uuid}/show', name: 'edr_parent_facture_show', methods: ['GET'])]
    public function show(Facture $facture): Response
    {
        if (($hasTuteur = $this->hasTuteur()) instanceof Response) {
            return $hasTuteur;
        }

        $html = $this->factureRender->render($facture);

        return $this->render(
            '@AcMarcheEdrParent/facture/show.html.twig',
            [
                'facture' => $facture,
                'content' => $html,
            ]
        );
    }
}
