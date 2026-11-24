<?php

namespace AcMarche\Edr\Controller\Admin;

use AcMarche\Edr\Ecole\Form\EcoleType;
use AcMarche\Edr\Ecole\Message\EcoleCreated;
use AcMarche\Edr\Ecole\Message\EcoleDeleted;
use AcMarche\Edr\Ecole\Message\EcoleUpdated;
use AcMarche\Edr\Ecole\Repository\EcoleRepository;
use AcMarche\Edr\Enfant\Repository\EnfantRepository;
use AcMarche\Edr\Entity\Scolaire\Ecole;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/ecole')]
#[IsGranted('ROLE_MERCREDI_ADMIN')]
final class EcoleController extends AbstractController
{
    public function __construct(
        private readonly EcoleRepository $ecoleRepository,
        private readonly EnfantRepository $enfantRepository,
        private readonly MessageBusInterface $dispatcher
    ) {
    }

    #[Route(path: '/', name: 'edr_admin_ecole_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render(
            '@AcMarcheEdrAdmin/ecole/index.html.twig',
            [
                'ecoles' => $this->ecoleRepository->findAllOrderByNom(),
            ]
        );
    }

    #[Route(path: '/new', name: 'edr_admin_ecole_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $ecole = new Ecole();
        $form = $this->createForm(EcoleType::class, $ecole);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->ecoleRepository->persist($ecole);
            $this->ecoleRepository->flush();

            $this->dispatcher->dispatch(new EcoleCreated($ecole->getId()));

            return $this->redirectToRoute('edr_admin_ecole_show', [
                'id' => $ecole->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/ecole/new.html.twig',
            [
                'ecole' => $ecole,
                'form' => $form,
            ]
        );
    }

    #[Route(path: '/{id}', name: 'edr_admin_ecole_show', methods: ['GET'])]
    public function show(Ecole $ecole): Response
    {
        $enfants = $this->enfantRepository->findByEcoles([$ecole]);

        return $this->render(
            '@AcMarcheEdrAdmin/ecole/show.html.twig',
            [
                'ecole' => $ecole,
                'enfants' => $enfants,
            ]
        );
    }

    #[Route(path: '/{id}/edit', name: 'edr_admin_ecole_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Ecole $ecole): Response
    {
        $form = $this->createForm(EcoleType::class, $ecole);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->ecoleRepository->flush();

            $this->dispatcher->dispatch(new EcoleUpdated($ecole->getId()));

            return $this->redirectToRoute('edr_admin_ecole_show', [
                'id' => $ecole->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/ecole/edit.html.twig',
            [
                'ecole' => $ecole,
                'form' => $form,
            ]
        );
    }

    #[Route(path: '/{id}/delete', name: 'edr_admin_ecole_delete', methods: ['POST'])]
    public function delete(Request $request, Ecole $ecole): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete' . $ecole->getId(), $request->request->get('_token'))) {
            if ([] !== $this->enfantRepository->findByEcoles([$ecole])) {
                $this->addFlash('danger', 'L\'école contient des enfants et ne peut être supprimée');

                return $this->redirectToRoute('edr_admin_ecole_show', [
                    'id' => $ecole->getId(),
                ]);
            }

            $ecoleId = $ecole->getId();
            $this->ecoleRepository->remove($ecole);
            $this->ecoleRepository->flush();
            $this->dispatcher->dispatch(new EcoleDeleted($ecoleId));
        }

        return $this->redirectToRoute('edr_admin_ecole_index');
    }
}
