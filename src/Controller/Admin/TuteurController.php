<?php

namespace AcMarche\Edr\Controller\Admin;

use AcMarche\Edr\Entity\Tuteur;
use AcMarche\Edr\Relation\Repository\RelationRepository;
use AcMarche\Edr\Search\SearchHelper;
use AcMarche\Edr\Tuteur\Form\SearchTuteurType;
use AcMarche\Edr\Tuteur\Form\TuteurType;
use AcMarche\Edr\Tuteur\Message\TuteurCreated;
use AcMarche\Edr\Tuteur\Message\TuteurDeleted;
use AcMarche\Edr\Tuteur\Message\TuteurUpdated;
use AcMarche\Edr\Tuteur\Repository\TuteurRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/tuteur')]
#[IsGranted(data: 'ROLE_MERCREDI_ADMIN')]
final class TuteurController extends AbstractController
{
    public function __construct(
        private readonly TuteurRepository $tuteurRepository,
        private readonly RelationRepository $relationRepository,
        private readonly SearchHelper $searchHelper,
        private readonly MessageBusInterface $dispatcher
    ) {
    }

    #[Route(path: '/', name: 'edr_admin_tuteur_index', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $form = $this->createForm(SearchTuteurType::class);
        $form->handleRequest($request);

        $tuteurs = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->searchHelper->saveSearch(SearchHelper::TUTEUR_LIST, $data);
            $tuteurs = $this->tuteurRepository->search($data['nom'], $data['archived']);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/tuteur/index.html.twig',
            [
                'tuteurs' => $tuteurs,
                'form' => $form->createView(),
                'search' => $form->isSubmitted()
            ]
        );
    }

    #[Route(path: '/new', name: 'edr_admin_tuteur_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $tuteur = new Tuteur();
        $form = $this->createForm(TuteurType::class, $tuteur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->tuteurRepository->persist($tuteur);
            $this->tuteurRepository->flush();

            $this->dispatcher->dispatch(new TuteurCreated($tuteur->getId()));

            return $this->redirectToRoute('edr_admin_tuteur_show', [
                'id' => $tuteur->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/tuteur/new.html.twig',
            [
                'tuteur' => $tuteur,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'edr_admin_tuteur_show', methods: ['GET'])]
    public function show(Tuteur $tuteur): Response
    {
        $relations = $this->relationRepository->findByTuteur($tuteur);

        return $this->render(
            '@AcMarcheEdrAdmin/tuteur/show.html.twig',
            [
                'tuteur' => $tuteur,
                'relations' => $relations,
            ]
        );
    }

    #[Route(path: '/{id}/edit', name: 'edr_admin_tuteur_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Tuteur $tuteur): Response
    {
        $form = $this->createForm(TuteurType::class, $tuteur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->tuteurRepository->flush();

            $this->dispatcher->dispatch(new TuteurUpdated($tuteur->getId()));

            return $this->redirectToRoute('edr_admin_tuteur_show', [
                'id' => $tuteur->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/tuteur/edit.html.twig',
            [
                'tuteur' => $tuteur,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}/delete', name: 'edr_admin_tuteur_delete', methods: ['POST'])]
    public function delete(Request $request, Tuteur $tuteur): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$tuteur->getId(), $request->request->get('_token'))) {
            /*      if (count($this->presenceRepository->findByTuteur($tuteur)) > 0) {
                      $this->addFlash('danger', 'Ce tuteur ne peut pas être supprimé car il y a des présences à son nom');

                      return $this->redirectToRoute('edr_admin_tuteur_show', ['id' => $tuteur->getId()]);
                  }*/

            $id = $tuteur->getId();
            $this->tuteurRepository->remove($tuteur);
            $this->tuteurRepository->flush();
            $this->dispatcher->dispatch(new TuteurDeleted($id));
        }

        return $this->redirectToRoute('edr_admin_tuteur_index');
    }
}
