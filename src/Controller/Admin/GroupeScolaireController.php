<?php

namespace AcMarche\Edr\Controller\Admin;

use AcMarche\Edr\Entity\Scolaire\GroupeScolaire;
use AcMarche\Edr\Scolaire\Form\GroupeScolaireType;
use AcMarche\Edr\Scolaire\Message\GroupeScolaireCreated;
use AcMarche\Edr\Scolaire\Message\GroupeScolaireDeleted;
use AcMarche\Edr\Scolaire\Message\GroupeScolaireUpdated;
use AcMarche\Edr\Scolaire\Repository\GroupeScolaireRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/groupe_scolaire')]
#[IsGranted(data: 'ROLE_MERCREDI_ADMIN')]
final class GroupeScolaireController extends AbstractController
{
    public function __construct(
        private readonly GroupeScolaireRepository $groupeScolaireRepository,
        private readonly MessageBusInterface $dispatcher
    ) {
    }

    #[Route(path: '/', name: 'edr_admin_groupe_scolaire_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render(
            '@AcMarcheEdrAdmin/groupe_scolaire/index.html.twig',
            [
                'groupes' => $this->groupeScolaireRepository->findAllOrderByOrdre(),
            ]
        );
    }

    #[Route(path: '/new', name: 'edr_admin_groupe_scolaire_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $groupeScolaire = new GroupeScolaire();
        $form = $this->createForm(GroupeScolaireType::class, $groupeScolaire);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->groupeScolaireRepository->persist($groupeScolaire);
            $this->groupeScolaireRepository->flush();

            $this->dispatcher->dispatch(new GroupeScolaireCreated($groupeScolaire->getId()));

            return $this->redirectToRoute('edr_admin_groupe_scolaire_show', [
                'id' => $groupeScolaire->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/groupe_scolaire/new.html.twig',
            [
                'groupe' => $groupeScolaire,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'edr_admin_groupe_scolaire_show', methods: ['GET'])]
    public function show(GroupeScolaire $groupeScolaire): Response
    {
        return $this->render(
            '@AcMarcheEdrAdmin/groupe_scolaire/show.html.twig',
            [
                'groupe_scolaire' => $groupeScolaire,
            ]
        );
    }

    #[Route(path: '/{id}/edit', name: 'edr_admin_groupe_scolaire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, GroupeScolaire $groupeScolaire): Response
    {
        $form = $this->createForm(GroupeScolaireType::class, $groupeScolaire);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->groupeScolaireRepository->flush();

            $this->dispatcher->dispatch(new GroupeScolaireUpdated($groupeScolaire->getId()));

            return $this->redirectToRoute('edr_admin_groupe_scolaire_show', [
                'id' => $groupeScolaire->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/groupe_scolaire/edit.html.twig',
            [
                'groupe_scolaire' => $groupeScolaire,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}/delete', name: 'edr_admin_groupe_scolaire_delete', methods: ['POST'])]
    public function delete(Request $request, GroupeScolaire $groupeScolaire): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete' . $groupeScolaire->getId(), $request->request->get('_token'))) {
            $ecoleId = $groupeScolaire->getId();
            $this->groupeScolaireRepository->remove($groupeScolaire);
            $this->groupeScolaireRepository->flush();
            $this->dispatcher->dispatch(new GroupeScolaireDeleted($ecoleId));
        }

        return $this->redirectToRoute('edr_admin_groupe_scolaire_index');
    }
}
