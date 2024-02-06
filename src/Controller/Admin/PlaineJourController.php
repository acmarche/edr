<?php

namespace AcMarche\Edr\Controller\Admin;

use AcMarche\Edr\Entity\Jour;
use AcMarche\Edr\Entity\Plaine\Plaine;
use AcMarche\Edr\Plaine\Form\PlaineJoursType;
use AcMarche\Edr\Plaine\Handler\PlaineAdminHandler;
use AcMarche\Edr\Plaine\Repository\PlainePresenceRepository;
use AcMarche\Edr\Scolaire\Grouping\GroupingInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/plaine_jour')]
#[IsGranted('ROLE_MERCREDI_ADMIN')]
final class PlaineJourController extends AbstractController
{
    public function __construct(
        private readonly PlaineAdminHandler $plaineAdminHandler,
        private readonly PlainePresenceRepository $plainePresenceRepository,
        private readonly GroupingInterface $grouping
    ) {
    }

    #[Route(path: '/edit/{id}', name: 'edr_admin_plaine_jour_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Plaine $plaine): Response
    {
        $this->plaineAdminHandler->initJours($plaine);
        $form = $this->createForm(PlaineJoursType::class, $plaine);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $jours = $form->get('jours')->getData();
            $this->plaineAdminHandler->handleEditJours($plaine, $jours);

            $this->addFlash('success', 'les dates ont bien été enregistrées');

            return $this->redirectToRoute('edr_admin_plaine_show', [
                'id' => $plaine->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/plaine_jour/edit.html.twig',
            [
                'plaine' => $plaine,
                'form' => $form,
            ]
        );
    }

    #[Route(path: '/{id}', name: 'edr_admin_plaine_jour_show', methods: ['GET'])]
    public function show(Jour $jour): Response
    {
        $plaine = $jour->getPlaine();
        $enfants = $this->plainePresenceRepository->findEnfantsByJour($jour, $plaine);
        $data = $this->grouping->groupEnfantsForPlaine($plaine, $enfants);

        return $this->render(
            '@AcMarcheEdrAdmin/plaine_jour/show.html.twig',
            [
                'jour' => $jour,
                'plaine' => $plaine,
                'datas' => $data,
            ]
        );
    }
}
