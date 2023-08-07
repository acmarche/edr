<?php

namespace AcMarche\Edr\Controller\Ecole;

use AcMarche\Edr\Accueil\Repository\AccueilRepository;
use AcMarche\Edr\Enfant\Form\EnfantEditForEcoleType;
use AcMarche\Edr\Enfant\Message\EnfantUpdated;
use AcMarche\Edr\Enfant\Repository\EnfantRepository;
use AcMarche\Edr\Entity\Enfant;
use AcMarche\Edr\Organisation\Repository\OrganisationRepository;
use AcMarche\Edr\Presence\Repository\PresenceRepository;
use AcMarche\Edr\Relation\Repository\RelationRepository;
use AcMarche\Edr\Sante\Handler\SanteHandler;
use AcMarche\Edr\Sante\Repository\SanteQuestionRepository;
use AcMarche\Edr\Sante\Utils\SanteChecker;
use AcMarche\Edr\Search\Form\SearchEnfantEcoleType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;


#[Route(path: '/enfant')]
final class EnfantController extends AbstractController
{
    use GetEcolesTrait;

    public function __construct(
        private readonly EnfantRepository $enfantRepository,
        private readonly SanteHandler $santeHandler,
        private readonly SanteChecker $santeChecker,
        private readonly PresenceRepository $presenceRepository,
        private readonly AccueilRepository $accueilRepository,
        private readonly RelationRepository $relationRepository,
        private readonly SanteQuestionRepository $santeQuestionRepository,
        private readonly OrganisationRepository $organisationRepository,
        private readonly MessageBusInterface $dispatcher
    ) {
    }

    #[Route(path: '/', name: 'edr_ecole_enfant_index', methods: ['GET', 'POST'])]
    #[IsGranted(data: 'ROLE_MERCREDI_ECOLE')]
    public function index(Request $request): Response
    {
        if (($response = $this->hasEcoles()) instanceof Response) {
            return $response;
        }

        $nom = null;
        $accueil = true;
        $form = $this->createForm(SearchEnfantEcoleType::class, [
            'accueil' => true,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $nom = $data['nom'];
            $accueil = $data['accueil'];
        }

        $enfants = $this->enfantRepository->searchForEcole($this->ecoles, $nom, $accueil);

        return $this->render(
            '@AcMarcheEdrEcole/enfant/index.html.twig',
            [
                'enfants' => $enfants,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/show/{uuid}', name: 'edr_ecole_enfant_show', methods: ['GET'])]
    #[IsGranted(data: 'enfant_show', subject: 'enfant')]
    public function show(Enfant $enfant): Response
    {
        $santeFiche = $this->santeHandler->init($enfant);
        $ficheSanteComplete = $this->santeChecker->isComplete($santeFiche);
        $presences = $this->presenceRepository->findByEnfant($enfant);
        $accueils = $this->accueilRepository->findByEnfant($enfant);
        $relations = $this->relationRepository->findByEnfant($enfant);

        return $this->render(
            '@AcMarcheEdrEcole/enfant/show.html.twig',
            [
                'enfant' => $enfant,
                'presences' => $presences,
                'accueils' => $accueils,
                'relations' => $relations,
                'ficheSanteComplete' => $ficheSanteComplete,
            ]
        );
    }

    #[Route(path: '/{uuid}/edit', name: 'edr_ecole_enfant_edit', methods: ['GET', 'POST'])]
    #[IsGranted(data: 'enfant_edit', subject: 'enfant')]
    public function edit(Request $request, Enfant $enfant): Response
    {
        $form = $this->createForm(EnfantEditForEcoleType::class, $enfant);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->enfantRepository->flush();

            $this->dispatcher->dispatch(new EnfantUpdated($enfant->getId()));

            return $this->redirectToRoute('edr_ecole_enfant_index', [
                'uuid' => $enfant->getUuid(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrEcole/enfant/edit.html.twig',
            [
                'enfant' => $enfant,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/sante/{uuid}', name: 'edr_ecole_sante_fiche_show', methods: ['GET'])]
    #[IsGranted(data: 'enfant_show', subject: 'enfant')]
    public function santeFiche(Enfant $enfant): Response
    {
        $santeFiche = $this->santeHandler->init($enfant);
        if (! $santeFiche->getId()) {
            $this->addFlash('warning', 'Cette enfant n\'a pas encore de fiche santé');

            return $this->redirectToRoute('edr_ecole_enfant_show', [
                'uuid' => $enfant->getUuid(),
            ]);
        }

        $isComplete = $this->santeChecker->isComplete($santeFiche);
        $questions = $this->santeQuestionRepository->findAllOrberByPosition();
        $organisation = $this->organisationRepository->getOrganisation();

        return $this->render(
            '@AcMarcheEdrEcole/enfant/sante_fiche.html.twig',
            [
                'enfant' => $enfant,
                'sante_fiche' => $santeFiche,
                'is_complete' => $isComplete,
                'questions' => $questions,
                'organisation' => $organisation,
            ]
        );
    }
}
