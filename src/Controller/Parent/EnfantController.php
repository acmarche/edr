<?php

namespace AcMarche\Edr\Controller\Parent;

use AcMarche\Edr\Accueil\Repository\AccueilRepository;
use AcMarche\Edr\Enfant\Form\EnfantEditForParentType;
use AcMarche\Edr\Enfant\Handler\EnfantHandler;
use AcMarche\Edr\Enfant\Message\EnfantCreated;
use AcMarche\Edr\Enfant\Repository\EnfantRepository;
use AcMarche\Edr\Entity\Enfant;
use AcMarche\Edr\Mailer\Factory\AdminEmailFactory;
use AcMarche\Edr\Mailer\NotificationMailer;
use AcMarche\Edr\Plaine\Repository\PlainePresenceRepository;
use AcMarche\Edr\Presence\Repository\PresenceRepository;
use AcMarche\Edr\Relation\Utils\RelationUtils;
use AcMarche\Edr\Sante\Handler\SanteHandler;
use AcMarche\Edr\Sante\Utils\SanteChecker;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;


#[Route(path: '/enfant')]
final class EnfantController extends AbstractController
{
    use GetTuteurTrait;

    public function __construct(
        private EnfantRepository $enfantRepository,
        private SanteHandler $santeHandler,
        private RelationUtils $relationUtils,
        private SanteChecker $santeChecker,
        private PresenceRepository $presenceRepository,
        private PlainePresenceRepository $plainePresenceRepository,
        private AccueilRepository $accueilRepository,
        private EnfantHandler $enfantHandler,
        private AdminEmailFactory $adminEmailFactory,
        private NotificationMailer $notifcationMailer,
        private MessageBusInterface $dispatcher
    ) {
    }

    #[Route(path: '/', name: 'edr_parent_enfant_index', methods: ['GET'])]
    #[IsGranted(data: 'ROLE_MERCREDI_PARENT')]
    public function index(): Response
    {
        if (($hasTuteur = $this->hasTuteur()) !== null) {
            return $hasTuteur;
        }
        $enfants = $this->relationUtils->findEnfantsByTuteur($this->tuteur);
        $this->santeChecker->isCompleteForEnfants($enfants);

        return $this->render(
            '@AcMarcheEdrParent/enfant/index.html.twig',
            [
                'enfants' => $enfants,
                'year' => date('Y'),
            ]
        );
    }

    #[Route(path: '/new', name: 'edr_parent_enfant_new', methods: ['GET', 'POST'])]
    #[IsGranted(data: 'ROLE_MERCREDI_PARENT')]
    public function new(Request $request): Response
    {
        $this->hasTuteur();
        $enfant = new Enfant();
        $form = $this->createForm(EnfantEditForParentType::class, $enfant);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->enfantHandler->newHandle($enfant, $this->tuteur);
            $this->dispatcher->dispatch(new EnfantCreated($enfant->getId()));
            $enfant->setPhoto(null); //bug serialize
            $message = $this->adminEmailFactory->messageEnfantCreated($this->getUser(), $enfant);
            $this->notifcationMailer->sendAsEmailNotification($message);

            return $this->redirectToRoute('edr_parent_enfant_show', [
                'uuid' => $enfant->getUuid(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrParent/enfant/new.html.twig',
            [
                'enfant' => $enfant,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{uuid}', name: 'edr_parent_enfant_show', methods: ['GET'])]
    #[IsGranted(data: 'enfant_show', subject: 'enfant')]
    public function show(Enfant $enfant): Response
    {
        $santeFiche = $this->santeHandler->init($enfant);
        $ficheSanteComplete = $this->santeChecker->isComplete($santeFiche);
        $presences = $this->presenceRepository->findByEnfant($enfant);
        $plaines = $this->plainePresenceRepository->findPlainesByEnfant($enfant);
        $accueils = $this->accueilRepository->findByEnfant($enfant);

        return $this->render(
            '@AcMarcheEdrParent/enfant/show.html.twig',
            [
                'enfant' => $enfant,
                'presences' => $presences,
                'plaines' => $plaines,
                'accueils' => $accueils,
                'ficheSanteComplete' => $ficheSanteComplete,
            ]
        );
    }
}
