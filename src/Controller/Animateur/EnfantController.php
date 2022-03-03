<?php

namespace AcMarche\Edr\Controller\Animateur;

use AcMarche\Edr\Enfant\Form\SearchEnfantForAnimateurType;
use AcMarche\Edr\Enfant\Repository\EnfantRepository;
use AcMarche\Edr\Entity\Enfant;
use AcMarche\Edr\Presence\Repository\PresenceRepository;
use AcMarche\Edr\Relation\Repository\RelationRepository;
use AcMarche\Edr\Sante\Handler\SanteHandler;
use AcMarche\Edr\Sante\Utils\SanteChecker;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route(path: '/enfant')]
final class EnfantController extends AbstractController
{
    use GetAnimateurTrait;

    public function __construct(
        private EnfantRepository $enfantRepository,
        private SanteHandler $santeHandler,
        private SanteChecker $santeChecker,
        private PresenceRepository $presenceRepository,
        private RelationRepository $relationRepository
    ) {
    }

    #[Route(path: '/', name: 'edr_animateur_enfant_index', methods: ['GET', 'POST'])]
    #[IsGranted(data: 'ROLE_MERCREDI_ANIMATEUR')]
    public function index(Request $request): Response
    {
        if (($hasAnimateur = $this->hasAnimateur()) !== null) {
            return $hasAnimateur;
        }
        $nom = null;
        $form = $this->createForm(
            SearchEnfantForAnimateurType::class,
            null,
            [
                'animateur' => $this->animateur,
            ]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $nom = $data['nom'];
        }
        $enfants = $this->enfantRepository->searchForAnimateur($this->animateur, $nom);

        return $this->render(
            '@AcMarcheEdrAnimateur/enfant/index.html.twig',
            [
                'enfants' => $enfants,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{uuid}', name: 'edr_animateur_enfant_show', methods: ['GET'])]
    #[IsGranted(data: 'enfant_show', subject: 'enfant')]
    public function show(Enfant $enfant): Response
    {
        $santeFiche = $this->santeHandler->init($enfant);
        $ficheSanteComplete = $this->santeChecker->isComplete($santeFiche);
        $presences = $this->presenceRepository->findByEnfant($enfant);
        $relations = $this->relationRepository->findByEnfant($enfant);

        return $this->render(
            '@AcMarcheEdrAnimateur/enfant/show.html.twig',
            [
                'enfant' => $enfant,
                'presences' => $presences,
                'relations' => $relations,
                'ficheSanteComplete' => $ficheSanteComplete,
            ]
        );
    }
}
