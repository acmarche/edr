<?php

namespace AcMarche\Edr\Controller\Admin;


use AcMarche\Edr\Contrat\Facture\FactureHandlerInterface;
use AcMarche\Edr\Contrat\Presence\PresenceCalculatorInterface;
use AcMarche\Edr\Entity\Facture\Facture;
use AcMarche\Edr\Entity\Facture\FacturePresence;
use AcMarche\Edr\Facture\FactureInterface;
use AcMarche\Edr\Facture\Form\FactureAttachType;
use AcMarche\Edr\Facture\Form\FactureEditType;
use AcMarche\Edr\Facture\Repository\FacturePresenceNonPayeRepository;
use AcMarche\Edr\Facture\Repository\FacturePresenceRepository;
use AcMarche\Edr\Presence\Repository\PresenceRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[IsGranted('ROLE_MERCREDI_ADMIN')]
#[Route(path: '/facture_presence')]
final class FacturePresenceController extends AbstractController
{
    public function __construct(
        private readonly FacturePresenceRepository $facturePresenceRepository,
        private readonly FactureHandlerInterface $factureHandler,
        private readonly FacturePresenceNonPayeRepository $facturePresenceNonPayeRepository,
        public PresenceCalculatorInterface $presenceCalculator,
        private readonly PresenceRepository $presenceRepository,
    ) {
    }

    #[Route(path: '/{id}/attach', name: 'edr_admin_facture_presence_attach', methods: ['GET', 'POST'])]
    public function attach(Request $request, Facture $facture): Response
    {
        $tuteur = $facture->getTuteur();
        $presences = $this->facturePresenceNonPayeRepository->findPresencesNonPayes($tuteur);
        $form = $this->createForm(FactureAttachType::class, $facture);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $presencesF = (array) $request->request->all('presences');
            $this->factureHandler->handleManually($facture, $presencesF, []);

            $this->addFlash('success', 'Les présences ont bien été attachées');

            return $this->redirectToRoute('edr_admin_facture_show', [
                'id' => $facture->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/facture_presence/attach.html.twig',
            [
                'facture' => $facture,
                'tuteur' => $facture->getTuteur(),
                'presences' => $presences,
                'form' => $form,
            ]
        );
    }

    #[Route(path: '/{id}/show', name: 'edr_admin_facture_presence_show', methods: ['GET'])]
    public function show(FacturePresence $facturePresence): Response
    {
        $facture = $facturePresence->getFacture();
        $presence = null;
        $accueil = null;
        $type = $facturePresence->getObjectType();
        if (FactureInterface::OBJECT_PRESENCE === $type || FactureInterface::OBJECT_PLAINE === $type) {
            $presence = $this->presenceRepository->find($facturePresence->getPresenceId());
        }

        return $this->render(
            '@AcMarcheEdrAdmin/facture_presence/show.html.twig',
            [
                'facture' => $facture,
                'facturePresence' => $facturePresence,
                'presence' => $presence,
                'accueil' => $accueil,
            ]
        );
    }

    /**
     * Route("/{id}/edit", name="edr_admin_facture_presence_edit", methods={"GET","POST"}).
     */
    public function edit(Request $request, Facture $facture): Response
    {
        $form = $this->createForm(FactureEditType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //todo
            echo '';
        }

        return $this->render(
            '@AcMarcheEdrAdmin/facture/edit.html.twig',
            [
                'facture' => $facture,
                'form' => $form,
            ]
        );
    }

    #[Route(path: '/{id}/delete', name: 'edr_admin_facture_presence_delete', methods: ['POST'])]
    public function delete(Request $request, FacturePresence $facturePresence): RedirectResponse
    {
        $facture = $facturePresence->getFacture();
        if ($this->isCsrfTokenValid('delete' . $facturePresence->getId(), $request->request->get('_token'))) {
            $this->facturePresenceRepository->remove($facturePresence);
            $this->facturePresenceRepository->flush();

            $this->addFlash('success', 'La présence a bien été détachée');
        }

        return $this->redirectToRoute('edr_admin_facture_show', [
            'id' => $facture->getId(),
        ]);
    }
}
