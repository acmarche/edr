<?php

namespace AcMarche\Edr\Controller\Admin;

use AcMarche\Edr\Entity\Reduction;
use AcMarche\Edr\Reduction\Form\ReductionType;
use AcMarche\Edr\Reduction\Message\ReductionCreated;
use AcMarche\Edr\Reduction\Message\ReductionDeleted;
use AcMarche\Edr\Reduction\Message\ReductionUpdated;
use AcMarche\Edr\Reduction\Repository\ReductionRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/reduction')]
#[IsGranted(data: 'ROLE_MERCREDI_ADMIN')]
final class ReductionController extends AbstractController
{
    public function __construct(
        private ReductionRepository $reductionRepository,
        private MessageBusInterface $dispatcher
    ) {
    }

    #[Route(path: '/', name: 'edr_admin_reduction_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render(
            '@AcMarcheEdrAdmin/reduction/index.html.twig',
            [
                'reductions' => $this->reductionRepository->findAll(),
            ]
        );
    }

    #[Route(path: '/new', name: 'edr_admin_reduction_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $reduction = new Reduction();
        $form = $this->createForm(ReductionType::class, $reduction);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->reductionRepository->persist($reduction);
            $this->reductionRepository->flush();

            $this->dispatcher->dispatch(new ReductionCreated($reduction->getId()));

            return $this->redirectToRoute('edr_admin_reduction_show', [
                'id' => $reduction->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/reduction/new.html.twig',
            [
                'reduction' => $reduction,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'edr_admin_reduction_show', methods: ['GET'])]
    public function show(Reduction $reduction): Response
    {
        return $this->render(
            '@AcMarcheEdrAdmin/reduction/show.html.twig',
            [
                'reduction' => $reduction,
            ]
        );
    }

    #[Route(path: '/{id}/edit', name: 'edr_admin_reduction_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reduction $reduction): Response
    {
        $form = $this->createForm(ReductionType::class, $reduction);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->reductionRepository->flush();

            $this->dispatcher->dispatch(new ReductionUpdated($reduction->getId()));

            return $this->redirectToRoute('edr_admin_reduction_show', [
                'id' => $reduction->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/reduction/edit.html.twig',
            [
                'reduction' => $reduction,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}/delete', name: 'edr_admin_reduction_delete', methods: ['POST'])]
    public function delete(Request $request, Reduction $reduction): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$reduction->getId(), $request->request->get('_token'))) {
            $id = $reduction->getId();
            $this->reductionRepository->remove($reduction);
            $this->reductionRepository->flush();
            $this->dispatcher->dispatch(new ReductionDeleted($id));
        }

        return $this->redirectToRoute('edr_admin_reduction_index');
    }
}
