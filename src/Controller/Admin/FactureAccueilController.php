<?php

namespace AcMarche\Edr\Controller\Admin;

use AcMarche\Edr\Contrat\Facture\FactureHandlerInterface;
use AcMarche\Edr\Entity\Facture\Facture;
use AcMarche\Edr\Facture\Form\FactureAttachType;
use AcMarche\Edr\Facture\Repository\FacturePresenceNonPayeRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[IsGranted(data: 'ROLE_MERCREDI_ADMIN')]
#[Route(path: '/facture_accueil')]
final class FactureAccueilController extends AbstractController
{
    public function __construct(
        private readonly FactureHandlerInterface $factureHandler,
        private readonly FacturePresenceNonPayeRepository $facturePresenceNonPayeRepository
    ) {
    }

    #[Route(path: '/{id}/attach', name: 'edr_admin_facture_accueil_attach', methods: ['GET', 'POST'])]
    public function attach(Request $request, Facture $facture): Response
    {
        $tuteur = $facture->getTuteur();
        $accueils = $this->facturePresenceNonPayeRepository->findAccueilsNonPayes($tuteur);
        $form = $this->createForm(FactureAttachType::class, $facture);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $accueilsF = (array) $request->request->all('accueils');
            $this->factureHandler->handleManually($facture, [], $accueilsF);

            $this->addFlash('success', 'Les accueils ont bien été attachés');

            return $this->redirectToRoute('edr_admin_facture_show', [
                'id' => $facture->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/facture_accueil/attach.html.twig',
            [
                'facture' => $facture,
                'tuteur' => $facture->getTuteur(),
                'accueils' => $accueils,
                'form' => $form->createView(),
            ]
        );
    }
}
