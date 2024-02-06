<?php

namespace AcMarche\Edr\Controller\Admin;

use AcMarche\Edr\Enfant\Form\EnfantType;
use AcMarche\Edr\Enfant\Form\SearchEnfantType;
use AcMarche\Edr\Enfant\Handler\EnfantHandler;
use AcMarche\Edr\Enfant\Message\EnfantCreated;
use AcMarche\Edr\Enfant\Message\EnfantDeleted;
use AcMarche\Edr\Enfant\Message\EnfantUpdated;
use AcMarche\Edr\Enfant\Repository\EnfantRepository;
use AcMarche\Edr\Entity\Enfant;
use AcMarche\Edr\Entity\Tuteur;
use AcMarche\Edr\Plaine\Repository\PlainePresenceRepository;
use AcMarche\Edr\Presence\Repository\PresenceRepository;
use AcMarche\Edr\Presence\Utils\PresenceUtils;
use AcMarche\Edr\Relation\Repository\RelationRepository;
use AcMarche\Edr\Search\SearchHelper;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/enfant')]
#[IsGranted('ROLE_MERCREDI_ADMIN')]
final class EnfantController extends AbstractController
{
    public function __construct(
        private readonly EnfantRepository $enfantRepository,
        private readonly EnfantHandler $enfantHandler,
        private readonly RelationRepository $relationRepository,
        private readonly PresenceRepository $presenceRepository,
        private readonly PresenceUtils $presenceUtils,
        private readonly SearchHelper $searchHelper,
        private readonly PlainePresenceRepository $plainePresenceRepository,
        private readonly MessageBusInterface $dispatcher
    ) {
    }

    #[Route(path: '/', name: 'edr_admin_enfant_index', methods: ['GET', 'POST'])]
    #[Route(path: '/all/{all}', name: 'edr_admin_enfant_all', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $form = $this->createForm(SearchEnfantType::class);
        $form->handleRequest($request);

        $enfants = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->searchHelper->saveSearch(SearchHelper::ENFANT_LIST, $data);
            $enfants = $this->enfantRepository->search(
                $data['nom'],
                $data['ecole'],
                $data['annee_scolaire'],
                $data['archived']
            );
        }
$response = new Response(null, $form->isSubmitted() ? Response::HTTP_ACCEPTED : Response::HTTP_OK);
        return $this->render(
            '@AcMarcheEdrAdmin/enfant/index.html.twig',
            [
                'enfants' => $enfants,
                'form' => $form->createView(),
                'search' => $form->isSubmitted(),
            ]
        ,$response);
    }

    #[Route(path: '/new/{id}', name: 'edr_admin_enfant_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Tuteur $tuteur): Response
    {
        $enfant = new Enfant();
        $form = $this->createForm(EnfantType::class, $enfant);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->enfantHandler->newHandle($enfant, $tuteur);
            $this->dispatcher->dispatch(new EnfantCreated($enfant->getId()));

            return $this->redirectToRoute('edr_admin_enfant_show', [
                'id' => $enfant->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/enfant/new.html.twig',
            [
                'enfant' => $enfant,
                'form' => $form,
            ]
        );
    }

    #[Route(path: '/{id}', name: 'edr_admin_enfant_show', methods: ['GET'])]
    public function show(Enfant $enfant): Response
    {
        $relations = $this->relationRepository->findByEnfant($enfant);
        $data = $this->presenceRepository->findByEnfant($enfant);
        $presencesGrouped = $this->presenceUtils->groupByYear($data);
        $fratries = $this->relationRepository->findFrateries($enfant);
        $plaines = $this->plainePresenceRepository->findPlainesByEnfant($enfant);
        $year = date('Y');

        return $this->render(
            '@AcMarcheEdrAdmin/enfant/show.html.twig',
            [
                'enfant' => $enfant,
                'fratries' => $fratries,
                'relations' => $relations,
                'prensencesGrouped' => $presencesGrouped,
                'plaines' => $plaines,
                'currentYear' => $year,
            ]
        );
    }

    #[Route(path: '/{id}/edit', name: 'edr_admin_enfant_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Enfant $enfant): Response
    {
        $form = $this->createForm(EnfantType::class, $enfant);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->enfantRepository->flush();

            $this->dispatcher->dispatch(new EnfantUpdated($enfant->getId()));

            return $this->redirectToRoute('edr_admin_enfant_show', [
                'id' => $enfant->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/enfant/edit.html.twig',
            [
                'enfant' => $enfant,
                'form' => $form,
            ]
        );
    }

    #[Route(path: '/{id}/delete', name: 'edr_admin_enfant_delete', methods: ['POST'])]
    public function delete(Request $request, Enfant $enfant): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete' . $enfant->getId(), $request->request->get('_token'))) {
            $enfantId = $enfant->getId();
            $this->enfantRepository->remove($enfant);
            $this->enfantRepository->flush();
            $this->dispatcher->dispatch(new EnfantDeleted($enfantId));
        }

        return $this->redirectToRoute('edr_admin_enfant_index');
    }
}
