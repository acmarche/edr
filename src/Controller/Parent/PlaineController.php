<?php

namespace AcMarche\Edr\Controller\Parent;

use AcMarche\Edr\Contrat\Plaine\FacturePlaineHandlerInterface;
use AcMarche\Edr\Contrat\Plaine\PlaineHandlerInterface;
use AcMarche\Edr\Entity\Plaine\Plaine;
use AcMarche\Edr\Facture\Repository\FactureRepository;
use AcMarche\Edr\Mailer\Factory\AdminEmailFactory;
use AcMarche\Edr\Mailer\Factory\FactureEmailFactory;
use AcMarche\Edr\Mailer\NotificationMailer;
use AcMarche\Edr\Plaine\Form\PlaineConfirmationType;
use AcMarche\Edr\Plaine\Form\SelectEnfantType;
use AcMarche\Edr\Plaine\Repository\PlainePresenceRepository;
use AcMarche\Edr\Plaine\Repository\PlaineRepository;
use AcMarche\Edr\Presence\Utils\PresenceUtils;
use AcMarche\Edr\Relation\Utils\RelationUtils;
use AcMarche\Edr\Sante\Handler\SanteHandler;
use AcMarche\Edr\Sante\Utils\SanteChecker;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/plaine')]
final class PlaineController extends AbstractController
{
    use GetTuteurTrait;

    public function __construct(
        private readonly PlaineRepository $plaineRepository,
        private readonly RelationUtils $relationUtils,
        private readonly SanteHandler $santeHandler,
        private readonly SanteChecker $santeChecker,
        private readonly PlainePresenceRepository $plainePresenceRepository,
        private readonly FacturePlaineHandlerInterface $facturePlaineHandler,
        private readonly FactureEmailFactory $factureEmailFactory,
        private readonly NotificationMailer $notificationMailer,
        private readonly AdminEmailFactory $adminEmailFactory,
        private readonly PlaineHandlerInterface $plaineHandler,
        private readonly FactureRepository $factureRepository
    ) {
    }

    #[Route(path: '/open', name: 'edr_parent_plaine_open')]
    #[IsGranted(data: 'ROLE_MERCREDI_PARENT')]
    public function open(): Response
    {
        $plaine = $this->plaineRepository->findPlaineOpen();

        return $this->render(
            '@AcMarcheEdrParent/plaine/_open.html.twig',
            [
                'plaine' => $plaine,
            ]
        );
    }

    #[Route(path: '/{id}/show', name: 'edr_parent_plaine_show')]
    #[IsGranted(data: 'ROLE_MERCREDI_PARENT')]
    public function show(Plaine $plaine): Response
    {
        if (($hasTuteur = $this->hasTuteur()) instanceof Response) {
            return $hasTuteur;
        }

        $tuteur = $this->tuteur;
        $inscriptions = $this->plainePresenceRepository->findByPlaineAndTuteur($plaine, $tuteur);
        $enfantsInscrits = PresenceUtils::extractEnfants($inscriptions);
        $enfants = $this->relationUtils->findEnfantsByTuteur($tuteur);
        $resteEnfant = \count($enfantsInscrits) !== \count($enfants);
        $facture = null;
        if ($this->plaineHandler->isRegistrationFinalized($plaine, $tuteur)) {
            $facture = $this->factureRepository->findByTuteurAndPlaine($tuteur, $plaine);
        }

        return $this->render(
            '@AcMarcheEdrParent/plaine/show.html.twig',
            [
                'plaine' => $plaine,
                'enfants' => $enfantsInscrits,
                'inscriptions' => $inscriptions,
                'resteEnfants' => $resteEnfant,
                'facture' => $facture,
            ]
        );
    }

    /**
     * Etape 1 select enfant.
     */
    #[Route(path: '/select/enfant', name: 'edr_parent_plaine_select_enfant', methods: ['GET', 'POST'])]
    public function selectEnfant(Request $request): Response
    {
        if (($hasTuteur = $this->hasTuteur()) instanceof Response) {
            return $hasTuteur;
        }

        $enfants = $this->relationUtils->findEnfantsByTuteur($this->tuteur);
        $form = $this->createForm(SelectEnfantType::class, null, [
            'enfants' => $enfants,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $plaine = $this->plaineRepository->findPlaineOpen();
            $enfantsSelected = $form->get('enfants')->getData();
            foreach ($enfantsSelected as $enfant) {
                $santeFiche = $this->santeHandler->init($enfant);

                if (!$this->santeChecker->isComplete($santeFiche)) {
                    $this->addFlash('danger', 'La fiche santé de ' . $enfant . ' doit être complétée');

                    continue;
                }

                if ($plaine instanceof Plaine) {
                    $this->plaineHandler->handleAddEnfant($plaine, $this->tuteur, $enfant);
                    $this->addFlash('success', $enfant . ' a bien été inscrits à la plaine');
                }
            }

            return $this->redirectToRoute(
                'edr_parent_plaine_presence_confirmation',
                [
                ]
            );
        }

        return $this->render(
            '@AcMarcheEdrParent/plaine/select_enfant.html.twig',
            [
                'enfants' => $enfants,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/confirmation', name: 'edr_parent_plaine_presence_confirmation', methods: ['GET', 'POST'])]
    public function confirmation(Request $request): Response
    {
        if (($hasTuteur = $this->hasTuteur()) instanceof Response) {
            return $hasTuteur;
        }

        $tuteur = $this->tuteur;
        $plaine = $this->plaineRepository->findPlaineOpen();
        if ($this->plaineHandler->isRegistrationFinalized($plaine, $tuteur)) {
            return $this->redirectToRoute('edr_parent_plaine_show', [
                'id' => $plaine->getId(),
            ]);
        }

        $enfantsInscrits = $this->plainePresenceRepository->findEnfantsByPlaineAndTuteur($plaine, $tuteur);
        $enfants = $this->relationUtils->findEnfantsByTuteur($tuteur);
        $form = $this->createForm(PlaineConfirmationType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->plaineHandler->confirm($plaine, $tuteur);
                $this->addFlash('success', 'La facture a bien été générée et envoyée sur votre mail');
            } catch (Exception $e) {
                $this->addFlash('danger', 'Erreur survenue: ' . $e->getMessage());
            }

            return $this->redirectToRoute('edr_parent_plaine_show', [
                'id' => $plaine->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrParent/plaine/confirmation.twig',
            [
                'plaine' => $plaine,
                'enfantsInscrits' => $enfantsInscrits,
                'enfants' => $enfants,
                'form' => $form->createView(),
            ]
        );
    }
}
