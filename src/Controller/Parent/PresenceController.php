<?php

namespace AcMarche\Edr\Controller\Parent;

use AcMarche\Edr\Contrat\Presence\PresenceCalculatorInterface;
use AcMarche\Edr\Contrat\Presence\PresenceHandlerInterface;
use AcMarche\Edr\Entity\Enfant;
use AcMarche\Edr\Entity\Presence\Presence;
use AcMarche\Edr\Facture\Repository\FacturePresenceRepository;
use AcMarche\Edr\Presence\Constraint\DeleteConstraint;
use AcMarche\Edr\Presence\Dto\PresenceSelectDays;
use AcMarche\Edr\Presence\Form\PresenceNewForParentType;
use AcMarche\Edr\Presence\Message\PresenceCreated;
use AcMarche\Edr\Presence\Message\PresenceDeleted;
use AcMarche\Edr\Presence\Repository\PresenceDaysProviderInterface;
use AcMarche\Edr\Presence\Repository\PresenceRepository;
use AcMarche\Edr\Relation\Utils\RelationUtils;
use AcMarche\Edr\Sante\Handler\SanteHandler;
use AcMarche\Edr\Sante\Utils\SanteChecker;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/presence')]
#[IsGranted('ROLE_MERCREDI_PARENT')]
final class PresenceController extends AbstractController
{
    use GetTuteurTrait;

    public function __construct(
        private readonly RelationUtils $relationUtils,
        private readonly PresenceRepository $presenceRepository,
        private readonly PresenceHandlerInterface $presenceHandler,
        private readonly SanteChecker $santeChecker,
        private readonly SanteHandler $santeHandler,
        private readonly PresenceCalculatorInterface $presenceCalculator,
        private readonly PresenceDaysProviderInterface $presenceDaysProvider,
        private readonly FacturePresenceRepository $facturePresenceRepository,
        private readonly MessageBusInterface $dispatcher
    ) {
    }

    /**
     * Etape 1 select enfant.
     */
    #[Route(path: '/select/enfant', name: 'edr_parent_presence_select_enfant', methods: ['GET'])]
    public function selectEnfant(): Response
    {
        if (($hasTuteur = $this->hasTuteur()) instanceof Response) {
            return $hasTuteur;
        }

        $enfants = $this->relationUtils->findEnfantsByTuteur($this->tuteur);

        return $this->render(
            '@AcMarcheEdrParent/presence/select_enfant.html.twig',
            [
                'enfants' => $enfants,
            ]
        );
    }

    /**
     * Etape 2.
     */
    #[Route(path: '/select/jour/{uuid}', name: 'edr_parent_presence_select_jours', methods: ['GET', 'POST'])]
    #[IsGranted('enfant_edit', subject: 'enfant')]
    public function selectJours(Request $request, Enfant $enfant): Response
    {
        if (($hasTuteur = $this->hasTuteur()) instanceof Response) {
            return $hasTuteur;
        }

        $santeFiche = $this->santeHandler->init($enfant);
        if (!$this->santeChecker->isComplete($santeFiche)) {
            $this->addFlash('danger', 'La fiche santé de votre enfant doit être complétée');

            return $this->redirectToRoute('edr_parent_sante_fiche_show', [
                'uuid' => $enfant->getUuid(),
            ]);
        }

        $presenceSelectDays = new PresenceSelectDays($enfant);
        $form = $this->createForm(PresenceNewForParentType::class, $presenceSelectDays);
        $dates = $this->presenceDaysProvider->getAllDaysToSubscribe($enfant);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $days = $form->getData()->getJours();

            $this->presenceHandler->handleNew($this->tuteur, $enfant, $days);

            $this->dispatcher->dispatch(new PresenceCreated($days));

            return $this->redirectToRoute('edr_parent_enfant_show', [
                'uuid' => $enfant->getUuid(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrParent/presence/select_jours.html.twig',
            [
                'enfant' => $enfant,
                'form' => $form,
                'dates' => $dates,
            ]
        );
    }

    #[Route(path: '/{uuid}', name: 'edr_parent_presence_show', methods: ['GET'])]
    #[IsGranted('presence_show', subject: 'presence')]
    public function show(Presence $presence): Response
    {
        $facturePresence = $this->facturePresenceRepository->findByPresence($presence);

        return $this->render(
            '@AcMarcheEdrParent/presence/show.html.twig',
            [
                'presence' => $presence,
                'facturePresence' => $facturePresence,
                'enfant' => $presence->getEnfant(),
            ]
        );
    }

    #[Route(path: '/{id}/delete', name: 'edr_parent_presence_delete', methods: ['POST'])]
    #[IsGranted('presence_edit', subject: 'presence')]
    public function delete(Request $request, Presence $presence): RedirectResponse
    {
        $enfant = $presence->getEnfant();
        if ($this->isCsrfTokenValid('delete' . $presence->getId(), $request->request->get('_token'))) {
            if (!DeleteConstraint::canBeDeleted($presence)) {
                $this->addFlash('danger', 'Une présence passée ne peut être supprimée');

                return $this->redirectToRoute('edr_parent_enfant_show', [
                    'uuid' => $enfant->getUuid(),
                ]);
            }

            $presenceId = $presence->getId();
            $this->presenceRepository->remove($presence);
            $this->presenceRepository->flush();
            $this->dispatcher->dispatch(new PresenceDeleted($presenceId));
        }

        return $this->redirectToRoute('edr_parent_enfant_show', [
            'uuid' => $enfant->getUuid(),
        ]);
    }
}
