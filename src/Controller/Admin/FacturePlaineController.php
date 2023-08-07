<?php

namespace AcMarche\Edr\Controller\Admin;

use AcMarche\Edr\Entity\Plaine\Plaine;
use AcMarche\Edr\Entity\Tuteur;
use AcMarche\Edr\Facture\Handler\FacturePlaineHandler;
use AcMarche\Edr\Form\ValidateForm;
use AcMarche\Edr\Plaine\Repository\PlainePresenceRepository;
use AcMarche\Edr\Relation\Repository\RelationRepository;
use AcMarche\Edr\Relation\Utils\RelationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted(data: 'ROLE_MERCREDI_ADMIN')]
#[Route(path: '/facture_plaine')]
class FacturePlaineController extends AbstractController
{
    public function __construct(
        private readonly FacturePlaineHandler $facturePlaineHandler,
        private readonly RelationRepository $relationRepository,
        private readonly PlainePresenceRepository $plainePresenceRepository
    ) {
    }

    #[Route(path: '/{id}/manual', name: 'edr_admin_facture_select_plaine', methods: ['GET', 'POST'])]
    public function selectPlaine(Request $request, Tuteur $tuteur): Response
    {
        $relations = $this->relationRepository->findByTuteur($tuteur);
        $enfants = RelationUtils::extractEnfants($relations);
        $plaines = [[]];
        foreach ($enfants as $enfant) {
            $plaines[] = $this->plainePresenceRepository->findPlainesByEnfant($enfant);
        }

        $plaines = array_merge(...$plaines);

        return $this->render(
            '@AcMarcheEdrAdmin/facture_plaine/select_plaine.html.twig',
            [
                'tuteur' => $tuteur,
                'plaines' => $plaines,
            ]
        );
    }

    #[Route(path: '/{tuteur}/{plaine}/manual', name: 'edr_admin_facture_new_plaine', methods: ['GET', 'POST'])]
    public function newManual(Request $request, Tuteur $tuteur, Plaine $plaine): Response
    {
        $presences = $this->plainePresenceRepository->findByPlaineAndTuteur($plaine, $tuteur);
        $form = $this->createForm(ValidateForm::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $facture = $this->facturePlaineHandler->newInstance($plaine, $tuteur);
            $this->facturePlaineHandler->handleManually($facture, $plaine);

            $this->addFlash('success', 'Facture générée');

            return $this->redirectToRoute('edr_admin_facture_show', [
                'id' => $facture->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/facture_plaine/new.html.twig',
            [
                'tuteur' => $tuteur,
                'plaine' => $plaine,
                'presences' => $presences,
                'form' => $form->createView(),
            ]
        );
    }
}
