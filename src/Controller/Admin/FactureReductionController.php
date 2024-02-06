<?php

namespace AcMarche\Edr\Controller\Admin;

use AcMarche\Edr\Entity\Facture\Facture;
use AcMarche\Edr\Entity\Facture\FactureReduction;
use AcMarche\Edr\Facture\Form\FactureReductionType;
use AcMarche\Edr\Facture\Repository\FactureReductionRepository;
use DateTimeInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[IsGranted('ROLE_MERCREDI_ADMIN')]
#[Route(path: '/facture_reduction')]
final class FactureReductionController extends AbstractController
{
    public function __construct(
        private readonly FactureReductionRepository $factureReductionRepository
    ) {
    }

    #[Route(path: '/{id}/new', name: 'edr_admin_facture_reduction_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Facture $facture): Response
    {
        if ($facture->getEnvoyeLe() instanceof DateTimeInterface) {
            $this->addFlash('danger', 'La facture a déjà été envoyée');

            return $this->redirectToRoute('edr_admin_facture_show', [
                'id' => $facture->getId(),
            ]);
        }

        $factureReduction = new FactureReduction($facture);
        $form = $this->createForm(FactureReductionType::class, $factureReduction);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->factureReductionRepository->persist($factureReduction);
            $this->factureReductionRepository->flush();

            $this->addFlash('success', 'La réduction a bien été ajoutée');

            return $this->redirectToRoute('edr_admin_facture_show', [
                'id' => $facture->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/facture_reduction/new.html.twig',
            [
                'facture' => $facture,
                'tuteur' => $facture->getTuteur(),
                'form' => $form,
            ]
        );
    }

    #[Route(path: '/{id}/show', name: 'edr_admin_facture_reduction_show', methods: ['GET'])]
    public function show(FactureReduction $factureReduction): Response
    {
        $facture = $factureReduction->getFacture();

        return $this->render(
            '@AcMarcheEdrAdmin/facture_reduction/show.html.twig',
            [
                'facture' => $facture,
                'factureReduction' => $factureReduction,
            ]
        );
    }

    #[Route(path: '/{id}/edit', name: 'edr_admin_facture_reduction_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, FactureReduction $factureReduction): Response
    {
        if ($factureReduction->getFacture()->getEnvoyeLe() instanceof DateTimeInterface) {
            $this->addFlash('danger', 'La facture a déjà été envoyée');

            return $this->redirectToRoute('edr_admin_facture_show', [
                'id' => $factureReduction->getFacture()->getId(),
            ]);
        }

        $form = $this->createForm(FactureReductionType::class, $factureReduction);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->factureReductionRepository->flush();
            $this->addFlash('success', 'La réduction a bien été modifiée');

            return $this->redirectToRoute(
                'edr_admin_facture_reduction_show',
                [
                    'id' => $factureReduction->getId(),
                ]
            );
        }

        return $this->render(
            '@AcMarcheEdrAdmin/facture_reduction/edit.html.twig',
            [
                'facture' => $factureReduction->getFacture(),
                'factureReduction' => $factureReduction,
                'form' => $form,
            ]
        );
    }

    #[Route(path: '/{id}/delete', name: 'edr_admin_facture_reduction_delete', methods: ['POST'])]
    public function delete(Request $request, FactureReduction $factureReduction): RedirectResponse
    {
        $facture = $factureReduction->getFacture();
        if ($this->isCsrfTokenValid('delete' . $factureReduction->getId(), $request->request->get('_token'))) {
            $this->factureReductionRepository->remove($factureReduction);
            $this->factureReductionRepository->flush();

            $this->addFlash('success', 'La réduction a bien été supprimée');
        }

        return $this->redirectToRoute('edr_admin_facture_show', [
            'id' => $facture->getId(),
        ]);
    }
}
