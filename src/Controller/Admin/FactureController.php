<?php

namespace AcMarche\Edr\Controller\Admin;

use AcMarche\Edr\Contrat\Facture\FactureCalculatorInterface;
use AcMarche\Edr\Contrat\Facture\FactureHandlerInterface;
use AcMarche\Edr\Contrat\Facture\FactureRenderInterface;
use AcMarche\Edr\Entity\Facture\Facture;
use AcMarche\Edr\Entity\Tuteur;
use AcMarche\Edr\Facture\FactureInterface;
use AcMarche\Edr\Facture\Form\FactureEditType;
use AcMarche\Edr\Facture\Form\FactureManualType;
use AcMarche\Edr\Facture\Form\FacturePayerType;
use AcMarche\Edr\Facture\Form\FactureSearchType;
use AcMarche\Edr\Facture\Form\FactureSelectMonthType;
use AcMarche\Edr\Facture\Message\FactureCreated;
use AcMarche\Edr\Facture\Message\FactureDeleted;
use AcMarche\Edr\Facture\Message\FacturesCreated;
use AcMarche\Edr\Facture\Message\FactureUpdated;
use AcMarche\Edr\Facture\Repository\FacturePresenceNonPayeRepository;
use AcMarche\Edr\Facture\Repository\FactureRepository;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted(data: 'ROLE_MERCREDI_ADMIN')]
#[Route(path: '/facture')]
final class FactureController extends AbstractController
{
    public function __construct(
        private readonly FactureRepository $factureRepository,
        private readonly FactureHandlerInterface $factureHandler,
        private readonly FacturePresenceNonPayeRepository $facturePresenceNonPayeRepository,
        private readonly FactureCalculatorInterface $factureCalculator,
        private readonly FactureRenderInterface $factureRender,
        private readonly MessageBusInterface $dispatcher
    ) {
    }

    #[Route(path: '/{id}/index', name: 'edr_admin_facture_index_by_tuteur', methods: ['GET', 'POST'])]
    public function indexByTuteur(Tuteur $tuteur): Response
    {
        $factures = $this->factureRepository->findFacturesByTuteur($tuteur);
        $form = $this->createForm(
            FactureSelectMonthType::class,
            null,
            [
                'action' => $this->generateUrl('edr_admin_facture_new_month', [
                    'id' => $tuteur->getId(),
                ]),
            ]
        );

        return $this->render(
            '@AcMarcheEdrAdmin/facture/index.html.twig',
            [
                'factures' => $factures,
                'tuteur' => $tuteur,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/search', name: 'edr_admin_facture_index', methods: ['GET', 'POST'])]
    public function search(Request $request): Response
    {
        $factures = [];
        $form = $this->createForm(FactureSearchType::class);
        $form->handleRequest($request);

        $total = 0;
        if ($form->isSubmitted() && $form->isValid()) {
            $dataForm = $form->getData();
            $factures = $this->factureRepository->search(
                $dataForm['numero'],
                $dataForm['tuteur'],
                $dataForm['enfant'],
                $dataForm['ecole'],
                $dataForm['plaine'],
                $dataForm['paye'],
                $dataForm['datePaiement'],
                $dataForm['mois'],
                $dataForm['communication'],
            );
        }

        foreach ($factures as $facture) {
            $facture->factureDetailDto = $this->factureCalculator->createDetail($facture);
            $total += $facture->factureDetailDto->total;
        }

        $formMonth = $this->createForm(
            FactureSelectMonthType::class,
            null,
            [
                'action' => $this->generateUrl('edr_admin_facture_new_month_all'),
            ]
        );

        return $this->render(
            '@AcMarcheEdrAdmin/facture/search.html.twig',
            [
                'factures' => $factures,
                'form' => $form->createView(),
                'formMonth' => $formMonth->createView(),
                'search' => $form->isSubmitted(),
                'total' => $total,
            ]
        );
    }

    #[Route(path: '/{id}/manual', name: 'edr_admin_facture_new_manual', methods: ['GET', 'POST'])]
    public function newManual(Request $request, Tuteur $tuteur): Response
    {
        $facture = $this->factureHandler->newFacture($tuteur);
        $presences = $this->facturePresenceNonPayeRepository->findPresencesNonPayes($tuteur);
        $accueils = $this->facturePresenceNonPayeRepository->findAccueilsNonPayes($tuteur);
        $form = $this->createForm(FactureManualType::class, $facture);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $presencesF = (array) $request->request->all('presences');
            $accueilsF = (array) $request->request->all('accueils');
            $this->factureHandler->handleManually($facture, $presencesF, $accueilsF);

            $this->dispatcher->dispatch(new FactureCreated($facture->getId()));

            return $this->redirectToRoute('edr_admin_facture_show', [
                'id' => $facture->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/facture/new.html.twig',
            [
                'tuteur' => $tuteur,
                'presences' => $presences,
                'accueils' => $accueils,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}/month/', name: 'edr_admin_facture_new_month', methods: ['GET', 'POST'])]
    public function newByMonth(Request $request, Tuteur $tuteur): RedirectResponse
    {
        $form = $this->createForm(FactureSelectMonthType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $month = $form->get('mois')->getData();

            if (!($facture = $this->factureHandler->generateByMonthForTuteur($tuteur, $month)) instanceof FactureInterface) {
                $this->addFlash('warning', 'Aucune présences ou accueils non facturés pour ce mois');

                return $this->redirectToRoute('edr_admin_facture_index_by_tuteur', [
                    'id' => $tuteur->getId(),
                ]);
            }

            $this->dispatcher->dispatch(new FactureCreated($facture->getId()));

            return $this->redirectToRoute('edr_admin_facture_show', [
                'id' => $facture->getId(),
            ]);
        }

        $this->addFlash('danger', 'Date non valide');

        return $this->redirectToRoute('edr_admin_facture_index_by_tuteur', [
            'id' => $tuteur->getId(),
        ]);
    }

    #[Route(path: '/for/all/', name: 'edr_admin_facture_new_month_all', methods: ['GET', 'POST'])]
    public function newByMonthForAll(Request $request): Response
    {
        $form = $this->createForm(FactureSelectMonthType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $month = $form->get('mois')->getData();

            try {
                $factures = $this->factureHandler->generateByMonthForEveryone($month);
            } catch (Exception $exception) {
                $this->addFlash('danger', 'Erreur survenue: ' . $exception->getMessage());

                return $this->redirectToRoute('edr_admin_facture_new_month_all');
            }

            if ([] === $factures) {
                $this->addFlash('warning', 'Aucune présences ou accueils non facturés pour ce mois');

                return $this->redirectToRoute('edr_admin_facture_new_month_all');
            }

            $this->dispatcher->dispatch(new FacturesCreated($factures));

            return $this->redirectToRoute('edr_admin_facture_index');
        }

        return $this->render(
            '@AcMarcheEdrAdmin/facture/generate.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}/show', name: 'edr_admin_facture_show', methods: ['GET'])]
    public function show(Facture $facture): Response
    {
        $tuteur = $facture->getTuteur();
        $html = $this->factureRender->render($facture);

        return $this->render(
            '@AcMarcheEdrAdmin/facture/show.html.twig',
            [
                'facture' => $facture,
                'tuteur' => $tuteur,
                'content' => $html,
            ]
        );
    }

    #[Route(path: '/{id}/edit', name: 'edr_admin_facture_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Facture $facture): Response
    {
        $form = $this->createForm(FactureEditType::class, $facture);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->factureRepository->flush();

            $this->dispatcher->dispatch(new FactureUpdated($facture->getId()));

            return $this->redirectToRoute('edr_admin_facture_show', [
                'id' => $facture->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/facture/edit.html.twig',
            [
                'facture' => $facture,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{uuid}/payer', name: 'edr_admin_facture_payer', methods: ['GET', 'POST'])]
    public function payer(Request $request, Facture $facture): Response
    {
        $form = $this->createForm(FacturePayerType::class, $facture);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->factureRepository->flush();

            $this->addFlash('success', 'Facture payée');

            return $this->redirectToRoute('edr_admin_facture_show', [
                'id' => $facture->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/facture/payer.html.twig',
            [
                'facture' => $facture,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}/delete', name: 'edr_admin_facture_delete', methods: ['POST'])]
    public function delete(Request $request, Facture $facture): RedirectResponse
    {
        $tuteur = null;
        if ($this->isCsrfTokenValid('delete' . $facture->getId(), $request->request->get('_token'))) {
            $factureId = $facture->getId();
            $tuteur = $facture->getTuteur();
            $this->factureRepository->remove($facture);
            $this->factureRepository->flush();
            $this->dispatcher->dispatch(new FactureDeleted($factureId));
        }

        return $this->redirectToRoute('edr_admin_tuteur_show', [
            'id' => $tuteur->getId(),
        ]);
    }
}
