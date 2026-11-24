<?php

namespace AcMarche\Edr\Controller\Admin;

use AcMarche\Edr\Entity\Relation;
use AcMarche\Edr\Entity\Tuteur;
use AcMarche\Edr\Relation\Form\RelationType;
use AcMarche\Edr\Relation\Message\RelationCreated;
use AcMarche\Edr\Relation\Message\RelationDeleted;
use AcMarche\Edr\Relation\Message\RelationUpdated;
use AcMarche\Edr\Relation\RelationHandler;
use AcMarche\Edr\Relation\Repository\RelationRepository;
use Exception;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/relation')]
#[IsGranted('ROLE_MERCREDI_ADMIN')]
final class RelationController extends AbstractController
{
    public function __construct(
        private readonly RelationRepository $relationRepository,
        private readonly RelationHandler $relationHandler,
        private readonly MessageBusInterface $dispatcher
    ) {
    }

    #[Route(path: '/attach/enfant/{id}', name: 'edr_admin_relation_attach_enfant', methods: ['POST'])]
    public function attachEnfant(Request $request, Tuteur $tuteur): RedirectResponse
    {
        if ($this->isCsrfTokenValid('attachEnfant' . $tuteur->getId(), $request->request->get('_token'))) {
            $enfantId = (int) $request->request->get('enfantId');

            try {
                $relation = $this->relationHandler->handleAttachEnfant($tuteur, $enfantId);
                $this->dispatcher->dispatch(new RelationCreated($relation->getId()));
            } catch (Exception $e) {
                $this->addFlash('danger', $e->getMessage());

                return $this->redirectToRoute('edr_admin_tuteur_show', [
                    'id' => $tuteur->getId(),
                ]);
            }
        } else {
            $this->addFlash('danger', 'Formulaire non valide');
        }

        return $this->redirectToRoute('edr_admin_tuteur_show', [
            'id' => $tuteur->getId(),
        ]);
    }

    #[Route(path: '/{id}/edit', name: 'edr_admin_relation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Relation $relation): Response
    {
        $form = $this->createForm(RelationType::class, $relation);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->relationRepository->flush();

            $this->dispatcher->dispatch(new RelationUpdated($relation->getId()));

            return $this->redirectToRoute('edr_admin_enfant_show', [
                'id' => $relation->getEnfant()->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/relation/edit.html.twig',
            [
                'relation' => $relation,
                'form' => $form,
            ]
        );
    }

    #[Route(path: '/delete', name: 'edr_admin_relation_delete', methods: ['POST'])]
    public function delete(Request $request): RedirectResponse
    {
        $relationId = $request->request->get('relationid');
        if (!$relationId) {
            $this->addFlash('danger', 'Relation non trouvée');

            return $this->redirectToRoute('edr_admin_home');
        }

        $relation = $this->relationRepository->find($relationId);
        if (!$relation instanceof Relation) {
            $this->addFlash('danger', 'Relation non trouvée');

            return $this->redirectToRoute('edr_admin_home');
        }

        $tuteur = $relation->getTuteur();
        if ($this->isCsrfTokenValid('delete' . $relation->getId(), $request->request->get('_token'))) {
            $this->relationRepository->remove($relation);
            $this->relationRepository->flush();
            $this->dispatcher->dispatch(new RelationDeleted($relationId));
        }

        return $this->redirectToRoute('edr_admin_tuteur_show', [
            'id' => $tuteur->getId(),
        ]);
    }
}
