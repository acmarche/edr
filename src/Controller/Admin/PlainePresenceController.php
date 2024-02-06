<?php

namespace AcMarche\Edr\Controller\Admin;

use AcMarche\Edr\Contrat\Plaine\PlaineCalculatorInterface;
use AcMarche\Edr\Contrat\Plaine\PlaineHandlerInterface;
use AcMarche\Edr\Enfant\Repository\EnfantRepository;
use AcMarche\Edr\Entity\Enfant;
use AcMarche\Edr\Entity\Plaine\Plaine;
use AcMarche\Edr\Entity\Presence\Presence;
use AcMarche\Edr\Entity\Tuteur;
use AcMarche\Edr\Plaine\Dto\PlainePresencesDto;
use AcMarche\Edr\Plaine\Form\PlainePresenceEditType;
use AcMarche\Edr\Plaine\Form\PlainePresencesEditType;
use AcMarche\Edr\Plaine\Repository\PlainePresenceRepository;
use AcMarche\Edr\Presence\Message\PresenceUpdated;
use AcMarche\Edr\Presence\Utils\PresenceUtils;
use AcMarche\Edr\Relation\Repository\RelationRepository;
use AcMarche\Edr\Search\Form\SearchNameType;
use AcMarche\Edr\Utils\SortUtils;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/plaine/presence')]
#[IsGranted('ROLE_MERCREDI_ADMIN')]
final class PlainePresenceController extends AbstractController
{
    public function __construct(
        private readonly PlaineHandlerInterface $plaineHandler,
        private readonly EnfantRepository $enfantRepository,
        private readonly RelationRepository $relationRepository,
        private readonly PlainePresenceRepository $plainePresenceRepository,
        private readonly PlaineCalculatorInterface $plaineCalculator,
        private readonly MessageBusInterface $dispatcher
    ) {
    }

    #[Route(path: '/new/{id}', name: 'edr_admin_plaine_presence_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Plaine $plaine): Response
    {
        if (0 === \count($plaine->getJours())) {
            $this->addFlash('danger', "La plaine n'a aucune date");

            return $this->redirectToRoute('edr_admin_plaine_show', [
                'id' => $plaine->getId(),
            ]);
        }

        $nom = null;
        $form = $this->createForm(SearchNameType::class, null);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $nom = $form->get('nom')->getData();
        }

        $enfants = $nom ? $this->enfantRepository->findByName($nom) : $this->enfantRepository->findAllActif();

        return $this->render(
            '@AcMarcheEdrAdmin/plaine_presence/new.html.twig',
            [
                'enfants' => $enfants,
                'plaine' => $plaine,
                'form' => $form,
            ]
        );
    }

    #[Route(path: '/select/tuteur/{plaine}/{enfant}', name: 'edr_admin_plaine_presence_select_tuteur', methods: ['GET', 'POST'])]
    //#[Entity(data: 'plaine', expr: 'repository.find(plaine)')]
    //#[Entity(data: 'enfant', expr: 'repository.find(enfant)')]
    public function selectTuteur(Plaine $plaine, Enfant $enfant): Response
    {
        $tuteurs = $this->relationRepository->findTuteursByEnfant($enfant);
        if (1 === \count($tuteurs)) {
            $tuteur = $tuteurs[0];

            return $this->redirectToRoute(
                'edr_admin_plaine_presence_confirmation',
                [
                    'plaine' => $plaine->getId(),
                    'tuteur' => $tuteur->getId(),
                    'enfant' => $enfant->getId(),
                ]
            );
        }

        return $this->render(
            '@AcMarcheEdrAdmin/plaine_presence/select_tuteur.html.twig',
            [
                'enfant' => $enfant,
                'plaine' => $plaine,
                'tuteurs' => $tuteurs,
            ]
        );
    }

    #[Route(path: '/confirmation/{plaine}/{tuteur}/{enfant}', name: 'edr_admin_plaine_presence_confirmation', methods: ['GET', 'POST'])]
    //#[Entity(data: 'tuteur', expr: 'repository.find(tuteur)')]
    //#[Entity(data: 'plaine', expr: 'repository.find(plaine)')]
    //#[Entity(data: 'enfant', expr: 'repository.find(enfant)')]
    public function confirmation(Plaine $plaine, Tuteur $tuteur, Enfant $enfant): RedirectResponse
    {
        $this->plaineHandler->handleAddEnfant($plaine, $tuteur, $enfant);
        $this->addFlash('success', "L'enfant a bien été inscrit");

        return $this->redirectToRoute(
            'edr_admin_plaine_presence_show',
            [
                'plaine' => $plaine->getId(),
                'enfant' => $enfant->getId(),
            ]
        );
    }

    #[Route(path: '/{plaine}/{enfant}', name: 'edr_admin_plaine_presence_show', methods: ['GET'])]
    public function show(Plaine $plaine, Enfant $enfant): Response
    {
        $presences = $this->plainePresenceRepository->findByPlaineAndEnfant($plaine, $enfant);
        $presences = SortUtils::sortPresences($presences);

        $cout = $this->plaineCalculator->calculate($plaine, $presences);

        return $this->render(
            '@AcMarcheEdrAdmin/plaine_presence/show.html.twig',
            [
                'plaine' => $plaine,
                'enfant' => $enfant,
                'presences' => $presences,
                'cout' => $cout,
            ]
        );
    }

    #[Route(path: '/{plaine}/{presence}/edit', name: 'edr_admin_plaine_presence_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Plaine $plaine, Presence $presence): Response
    {
        $enfant = $presence->getEnfant();
        $form = $this->createForm(PlainePresenceEditType::class, $presence);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->plainePresenceRepository->flush();

            $this->dispatcher->dispatch(new PresenceUpdated($presence->getId()));

            return $this->redirectToRoute(
                'edr_admin_plaine_presence_show',
                [
                    'plaine' => $plaine->getId(),
                    'enfant' => $enfant->getId(),
                ]
            );
        }

        return $this->render(
            '@AcMarcheEdrAdmin/plaine_presence/edit.html.twig',
            [
                'plaine' => $plaine,
                'presence' => $presence,
                'enfant' => $enfant,
                'form' => $form,
            ]
        );
    }

    #[Route(path: '/{plaine}/{enfant}/jours', name: 'edr_admin_plaine_presence_jours', methods: ['GET', 'POST'])]
    public function jours(Request $request, Plaine $plaine, Enfant $enfant): Response
    {
        $jours = $plaine->getJours();
        $plainePresencesDto = new PlainePresencesDto($plaine, $enfant, $jours);
        $presences = $this->plainePresenceRepository->findByPlaineAndEnfant($plaine, $enfant);
        $currentJours = PresenceUtils::extractJours($presences);
        $plainePresencesDto->setJours($currentJours);
        $form = $this->createForm(PlainePresencesEditType::class, $plainePresencesDto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $new = $plainePresencesDto->getJours();
            if ([] === $presences) {
                $tuteurs = $this->relationRepository->findTuteursByEnfant($enfant);
                $tuteur = $tuteurs[0];
            } else {
                //todo bad
                $tuteur = $presences[0]->getTuteur();
            }

            $this->plaineHandler->handleEditPresences($tuteur, $enfant, $currentJours, $new);
            $this->addFlash('success', 'Les présences ont bien été modifiées');

            return $this->redirectToRoute(
                'edr_admin_plaine_presence_show',
                [
                    'plaine' => $plaine->getId(),
                    'enfant' => $enfant->getId(),
                ]
            );
        }

        return $this->render(
            '@AcMarcheEdrAdmin/plaine_presence/edit_presences.html.twig',
            [
                'plaine' => $plaine,
                'enfant' => $enfant,
                'form' => $form,
            ]
        );
    }

    #[Route(path: '/{id}/delete', name: 'edr_admin_plaine_presence_delete', methods: ['POST'])]
    public function delete(Request $request, Plaine $plaine): RedirectResponse
    {
        $enfant = null;
        if ($this->isCsrfTokenValid('deletePresence' . $plaine->getId(), $request->request->get('_token'))) {
            $presenceId = (int) $request->request->get('presence');
            if (0 === $presenceId) {
                $this->addFlash('danger', 'Référence à la présence non trouvée');

                return $this->redirectToRoute('edr_admin_plaine_index');
            }

            $presence = $this->plainePresenceRepository->find($presenceId);
            if (null === $presence) {
                $this->addFlash('danger', 'Présence non trouvée');

                return $this->redirectToRoute('edr_admin_plaine_index');
            }

            $enfant = $presence->getEnfant();
            $this->plainePresenceRepository->remove($presence);
            $this->plainePresenceRepository->flush();

            $this->addFlash('success', 'La présence à bien été supprimée');
        }

        return $this->redirectToRoute(
            'edr_admin_plaine_presence_show',
            [
                'plaine' => $plaine->getId(),
                'enfant' => $enfant->getId(),
            ]
        );
    }

    #[Route(path: '/{plaine}/{enfant}', name: 'edr_admin_plaine_presence_remove_enfant', methods: ['POST'])]
    public function remove(Request $request, Plaine $plaine, Enfant $enfant): RedirectResponse
    {
        if ($this->isCsrfTokenValid('remove' . $plaine->getId(), $request->request->get('_token'))) {
            $this->plaineHandler->removeEnfant($plaine, $enfant);
            $this->addFlash('success', 'L\'enfant a été retiré de la plaine');
        }

        return $this->redirectToRoute('edr_admin_plaine_show', [
            'id' => $plaine->getId(),
        ]);
    }
}
