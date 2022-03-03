<?php

namespace AcMarche\Edr\Controller\Admin;

use AcMarche\Edr\Entity\Plaine\Plaine;
use AcMarche\Edr\Entity\Plaine\PlaineGroupe;
use AcMarche\Edr\Plaine\Form\PlaineOpenType;
use AcMarche\Edr\Plaine\Form\PlaineType;
use AcMarche\Edr\Plaine\Form\SearchPlaineType;
use AcMarche\Edr\Plaine\Handler\PlaineAdminHandler;
use AcMarche\Edr\Plaine\Message\PlaineCreated;
use AcMarche\Edr\Plaine\Message\PlaineDeleted;
use AcMarche\Edr\Plaine\Message\PlaineUpdated;
use AcMarche\Edr\Plaine\Repository\PlainePresenceRepository;
use AcMarche\Edr\Plaine\Repository\PlaineRepository;
use AcMarche\Edr\Scolaire\Grouping\GroupingInterface;
use AcMarche\Edr\Scolaire\Repository\GroupeScolaireRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/plaine')]
#[IsGranted(data: 'ROLE_MERCREDI_ADMIN')]
final class PlaineController extends AbstractController
{
    public function __construct(
        private PlaineRepository $plaineRepository,
        private PlainePresenceRepository $plainePresenceRepository,
        private GroupeScolaireRepository $groupeScolaireRepository,
        private PlaineAdminHandler $plaineAdminHandler,
        private GroupingInterface $grouping,
        private MessageBusInterface $dispatcher
    ) {
    }

    #[Route(path: '/', name: 'edr_admin_plaine_index', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $nom = null;
        $archived = false;
        $form = $this->createForm(SearchPlaineType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $archived = $data['archived'];
            $nom = $data['nom'];
        }
        $plaines = $this->plaineRepository->search($nom, $archived);
        array_map(function ($plaine) {
            $plaine->enfants = $this->plainePresenceRepository->findEnfantsByPlaine($plaine);
        }, $plaines);

        return $this->render(
            '@AcMarcheEdrAdmin/plaine/index.html.twig',
            [
                'plaines' => $plaines,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/new', name: 'edr_admin_plaine_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $plaine = new Plaine();
        foreach ($this->groupeScolaireRepository->findAllForPlaineOrderByNom() as $groupe) {
            $plaineGroupe = new PlaineGroupe($plaine, $groupe);
            $plaine->addPlaineGroupe($plaineGroupe);
        }
        $form = $this->createForm(PlaineType::class, $plaine);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->plaineRepository->persist($plaine);
            $this->plaineRepository->flush();

            $this->dispatcher->dispatch(new PlaineCreated($plaine->getId()));

            return $this->redirectToRoute('edr_admin_plaine_jour_edit', [
                'id' => $plaine->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/plaine/new.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'edr_admin_plaine_show', methods: ['GET'])]
    public function show(Plaine $plaine): Response
    {
        if (\count($plaine->getJours()) < 1) {
            $this->addFlash('danger', 'La plaine doit contenir des dates');

            return $this->redirectToRoute('edr_admin_plaine_jour_edit', [
                'id' => $plaine->getId(),
            ]);
        }
        $enfants = $this->plainePresenceRepository->findEnfantsByPlaine($plaine);
        $data = $this->grouping->groupEnfantsForPlaine($plaine, $enfants);

        return $this->render(
            '@AcMarcheEdrAdmin/plaine/show.html.twig',
            [
                'plaine' => $plaine,
                'enfants' => $enfants,
                'datas' => $data,
            ]
        );
    }

    #[Route(path: '/{id}/edit', name: 'edr_admin_plaine_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Plaine $plaine): Response
    {
        $form = $this->createForm(PlaineType::class, $plaine);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->plaineRepository->flush();

            $this->dispatcher->dispatch(new PlaineUpdated($plaine->getId()));

            return $this->redirectToRoute('edr_admin_plaine_show', [
                'id' => $plaine->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/plaine/edit.html.twig',
            [
                'plaine' => $plaine,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}/open', name: 'edr_admin_plaine_open', methods: ['GET', 'POST'])]
    public function open(Request $request, Plaine $plaine): Response
    {
        $form = $this->createForm(PlaineOpenType::class, $plaine);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (($plaineOpen = $this->plaineAdminHandler->handleOpeningRegistrations($plaine)) === null) {
                $this->plaineRepository->flush();
                $this->dispatcher->dispatch(new PlaineUpdated($plaine->getId()));
            } else {
                $this->addFlash(
                    'danger',
                    'Les inscriptions n\'ont pas pu être ouvrir car la plaine '.$plaineOpen->getNom(
                    ).' est toujours ouverte'
                );
            }

            return $this->redirectToRoute('edr_admin_plaine_show', [
                'id' => $plaine->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/plaine/open.html.twig',
            [
                'plaine' => $plaine,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}/delete', name: 'edr_admin_plaine_delete', methods: ['POST'])]
    public function delete(Request $request, Plaine $plaine): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$plaine->getId(), $request->request->get('_token'))) {
            $plaineId = $plaine->getId();
            $this->plaineRepository->remove($plaine);
            $this->plaineRepository->flush();
            $this->dispatcher->dispatch(new PlaineDeleted($plaineId));
        }

        return $this->redirectToRoute('edr_admin_plaine_index');
    }
}
