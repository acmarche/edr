<?php

namespace AcMarche\Edr\Controller\Admin;

use AcMarche\Edr\Entity\Facture\Facture;
use AcMarche\Edr\Entity\Facture\FactureComplement;
use AcMarche\Edr\Facture\Form\FactureComplementType;
use AcMarche\Edr\Facture\Repository\FactureComplementRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[IsGranted('ROLE_MERCREDI_ADMIN')]
#[Route(path: '/facture_complement')]
final class FactureComplementController extends AbstractController
{
    public function __construct(
        private readonly FactureComplementRepository $factureComplementRepository
    ) {
    }

    #[Route(path: '/{id}/new', name: 'edr_admin_facture_complement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Facture $facture): Response
    {
        $factureComplement = new FactureComplement($facture);
        $form = $this->createForm(FactureComplementType::class, $factureComplement);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->factureComplementRepository->persist($factureComplement);
            $this->factureComplementRepository->flush();

            $this->addFlash('success', 'Le complément a bien été ajouté');

            return $this->redirectToRoute('edr_admin_facture_show', [
                'id' => $facture->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/facture_complement/new.html.twig',
            [
                'facture' => $facture,
                'tuteur' => $facture->getTuteur(),
                'form' => $form,
            ]
        );
    }

    #[Route(path: '/{id}/show', name: 'edr_admin_facture_complement_show', methods: ['GET'])]
    public function show(FactureComplement $factureComplement): Response
    {
        $facture = $factureComplement->getFacture();

        return $this->render(
            '@AcMarcheEdrAdmin/facture_complement/show.html.twig',
            [
                'facture' => $facture,
                'factureComplement' => $factureComplement,
            ]
        );
    }

    #[Route(path: '/{id}/edit', name: 'edr_admin_facture_complement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, FactureComplement $factureComplement): Response
    {
        $form = $this->createForm(FactureComplementType::class, $factureComplement);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->factureComplementRepository->flush();
            $this->addFlash('success', 'Le complément a bien été modifié');

            return $this->redirectToRoute(
                'edr_admin_facture_complement_show',
                [
                    'id' => $factureComplement->getId(),
                ]
            );
        }

        return $this->render(
            '@AcMarcheEdrAdmin/facture_complement/edit.html.twig',
            [
                'facture' => $factureComplement->getFacture(),
                'factureComplement' => $factureComplement,
                'form' => $form,
            ]
        );
    }

    #[Route(path: '/{id}/delete', name: 'edr_admin_facture_complement_delete', methods: ['POST'])]
    public function delete(Request $request, FactureComplement $factureComplement): RedirectResponse
    {
        $facture = $factureComplement->getFacture();
        if ($this->isCsrfTokenValid('delete' . $factureComplement->getId(), $request->request->get('_token'))) {
            $this->factureComplementRepository->remove($factureComplement);
            $this->factureComplementRepository->flush();

            $this->addFlash('success', 'Le complément a bien été supprimé');
        }

        return $this->redirectToRoute('edr_admin_facture_show', [
            'id' => $facture->getId(),
        ]);
    }
}
