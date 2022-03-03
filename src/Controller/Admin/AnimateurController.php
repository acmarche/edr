<?php

namespace AcMarche\Edr\Controller\Admin;

use AcMarche\Edr\Animateur\Form\AnimateurType;
use AcMarche\Edr\Animateur\Form\SearchAnimateurType;
use AcMarche\Edr\Animateur\Message\AnimateurCreated;
use AcMarche\Edr\Animateur\Message\AnimateurDeleted;
use AcMarche\Edr\Animateur\Message\AnimateurUpdated;
use AcMarche\Edr\Animateur\Repository\AnimateurRepository;
use AcMarche\Edr\Entity\Animateur;
use AcMarche\Edr\Search\SearchHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/animateur')]
#[IsGranted(data: 'ROLE_MERCREDI_ADMIN')]
final class AnimateurController extends AbstractController
{
    public function __construct(
        private AnimateurRepository $animateurRepository,
        private SearchHelper $searchHelper,
        private MessageBusInterface $dispatcher
    ) {
    }

    #[Route(path: '/', name: 'edr_admin_animateur_index', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $form = $this->createForm(SearchAnimateurType::class);
        $form->handleRequest($request);

        $animateurs = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->searchHelper->saveSearch(SearchHelper::TUTEUR_LIST, $data);
            $animateurs = $this->animateurRepository->search($data['nom']);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/animateur/index.html.twig',
            [
                'animateurs' => $animateurs,
                'form' => $form->createView(),
                'search' => $form->isSubmitted()
            ]
        );
    }

    #[Route(path: '/new', name: 'edr_admin_animateur_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $animateur = new Animateur();
        $form = $this->createForm(AnimateurType::class, $animateur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->animateurRepository->persist($animateur);
            $this->animateurRepository->flush();

            $this->dispatcher->dispatch(new AnimateurCreated($animateur->getId()));

            return $this->redirectToRoute('edr_admin_animateur_show', [
                'id' => $animateur->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/animateur/new.html.twig',
            [
                'animateur' => $animateur,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'edr_admin_animateur_show', methods: ['GET'])]
    public function show(Animateur $animateur): Response
    {
        return $this->render(
            '@AcMarcheEdrAdmin/animateur/show.html.twig',
            [
                'animateur' => $animateur,
            ]
        );
    }

    #[Route(path: '/{id}/edit', name: 'edr_admin_animateur_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Animateur $animateur): Response
    {
        $form = $this->createForm(AnimateurType::class, $animateur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->animateurRepository->flush();

            $this->dispatcher->dispatch(new AnimateurUpdated($animateur->getId()));

            return $this->redirectToRoute('edr_admin_animateur_show', [
                'id' => $animateur->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/animateur/edit.html.twig',
            [
                'animateur' => $animateur,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * //todo que faire si presence.
     */
    #[Route(path: '/{id}/delete', name: 'edr_admin_animateur_delete', methods: ['POST'])]
    public function delete(Request $request, Animateur $animateur): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$animateur->getId(), $request->request->get('_token'))) {
            $id = $animateur->getId();
            $this->animateurRepository->remove($animateur);
            $this->animateurRepository->flush();
            $this->dispatcher->dispatch(new AnimateurDeleted($id));
        }

        return $this->redirectToRoute('edr_admin_animateur_index');
    }
}
