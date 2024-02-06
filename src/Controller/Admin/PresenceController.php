<?php

namespace AcMarche\Edr\Controller\Admin;

use AcMarche\Edr\Contrat\Facture\FactureHandlerInterface;
use AcMarche\Edr\Contrat\Presence\PresenceCalculatorInterface;
use AcMarche\Edr\Contrat\Presence\PresenceHandlerInterface;
use AcMarche\Edr\Entity\Enfant;
use AcMarche\Edr\Entity\Jour;
use AcMarche\Edr\Entity\Presence\Presence;
use AcMarche\Edr\Entity\Tuteur;
use AcMarche\Edr\Facture\FactureInterface;
use AcMarche\Edr\Facture\Repository\FacturePresenceRepository;
use AcMarche\Edr\Presence\Dto\ListingPresenceByMonth;
use AcMarche\Edr\Presence\Dto\PresenceSelectDays;
use AcMarche\Edr\Presence\Form\PresenceNewType;
use AcMarche\Edr\Presence\Form\PresenceType;
use AcMarche\Edr\Presence\Form\SearchPresenceByMonthType;
use AcMarche\Edr\Presence\Form\SearchPresenceType;
use AcMarche\Edr\Presence\Message\PresenceCreated;
use AcMarche\Edr\Presence\Message\PresenceDeleted;
use AcMarche\Edr\Presence\Message\PresenceUpdated;
use AcMarche\Edr\Presence\Repository\PresenceRepository;
use AcMarche\Edr\Relation\Utils\OrdreService;
use AcMarche\Edr\Search\SearchHelper;
use AcMarche\Edr\Utils\DateUtils;
use Exception;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/presence')]
#[IsGranted('ROLE_MERCREDI_ADMIN')]
final class PresenceController extends AbstractController
{
    public function __construct(
        private readonly PresenceRepository $presenceRepository,
        private readonly PresenceHandlerInterface $presenceHandler,
        private readonly SearchHelper $searchHelper,
        private readonly ListingPresenceByMonth $listingPresenceByMonth,
        private readonly FacturePresenceRepository $facturePresenceRepository,
        private readonly FactureHandlerInterface $factureHandler,
        private readonly PresenceCalculatorInterface $presenceCalculator,
        private readonly OrdreService $ordreService,
        private readonly MessageBusInterface $dispatcher
    ) {
    }

    #[Route(path: '/', name: 'edr_admin_presence_index', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $data = [];
        $displayRemarque = false;
        $jour = null;
        $form = $this->createForm(SearchPresenceType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $dataForm = $form->getData();
            /** @var Jour $jour */
            $jour = $dataForm['jour'];
            $displayRemarque = $dataForm['displayRemarque'];
            $ecole = $dataForm['ecole'];
            $this->searchHelper->saveSearch(SearchHelper::PRESENCE_LIST, $dataForm);
            $data = $this->presenceHandler->searchAndGrouping($jour, $ecole, $displayRemarque);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/presence/index.html.twig',
            [
                'datas' => $data,
                'form' => $form,
                'search' => $form->isSubmitted(),
                'jour' => $jour,
                'display_remarques' => $displayRemarque,
            ]
        );
    }

    /**
     * Liste toutes les presences par mois.
     */
    #[Route(path: '/by/month', name: 'edr_admin_presence_by_month', methods: ['GET', 'POST'])]
    public function indexByMonth(Request $request): Response
    {
        $mois = null;
        $listingPresences = [];
        $form = $this->createForm(SearchPresenceByMonthType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $mois = $data['mois'];

            try {
                $date = DateUtils::createDateTimeFromDayMonth($mois);
            } catch (Exception $e) {
                $this->addFlash('danger', $e->getMessage());

                return $this->redirectToRoute('edr_admin_presence_by_month');
            }

            $listingPresences = $this->listingPresenceByMonth->create($date);
            $this->searchHelper->saveSearch(SearchHelper::PRESENCE_LIST_BY_MONTH, $data);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/presence/index_by_month.html.twig',
            [
                'form' => $form,
                'search_form' => $form,
                'search' => $form->isSubmitted(),
                'month' => $mois,
                'listingPresences' => $listingPresences,
            ]
        );
    }

    #[Route(path: '/new/{tuteur}/{enfant}', name: 'edr_admin_presence_new', methods: ['GET', 'POST'])]
    //#[Entity(data: 'tuteur', expr: 'repository.find(tuteur)')]
    // #[Entity(data: 'enfant', expr: 'repository.find(enfant)')]
    public function new(
        Request $request,
        #[MapEntity(class: Tuteur::class, expr: 'repository.find(tuteur)')]
        Tuteur $tuteur,
        #[MapEntity(class: Enfant::class, expr: 'repository.find(enfant)')] $enfant
    ): Response {
        $presenceSelectDays = new PresenceSelectDays($enfant);
        $form = $this->createForm(PresenceNewType::class, $presenceSelectDays);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $days = $form->getData()->getJours();

            $this->presenceHandler->handleNew($tuteur, $enfant, $days);

            $this->dispatcher->dispatch(new PresenceCreated($days));

            return $this->redirectToRoute('edr_admin_enfant_show', [
                'id' => $enfant->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/presence/new.html.twig',
            [
                'enfant' => $enfant,
                'tuteur' => $tuteur,
                'form' => $form,
            ]
        );
    }

    #[Route(path: '/{id}', name: 'edr_admin_presence_show', methods: ['GET'])]
    public function show(Presence $presence): Response
    {
        $facturePresence = $this->facturePresenceRepository->findByPresence($presence);
        $ordre = $this->presenceCalculator->getOrdreOnPresence($presence);
        $fratries = $this->ordreService->getFratriesPresents($presence);

        return $this->render(
            '@AcMarcheEdrAdmin/presence/show.html.twig',
            [
                'presence' => $presence,
                'facturePresence' => $facturePresence,
                'fratries' => $fratries,
                'ordre' => $ordre,
                'enfant' => $presence->getEnfant(),
            ]
        );
    }

    #[Route(path: '/{id}/edit', name: 'edr_admin_presence_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Presence $presence): Response
    {
        if ($this->factureHandler->isBilled($presence->getId(), FactureInterface::OBJECT_PRESENCE)) {
            $this->addFlash('danger', 'Une présence déjà facturée ne peut être éditée');

            return $this->redirectToRoute('edr_admin_presence_show', [
                'id' => $presence->getId(),
            ]);
        }

        $form = $this->createForm(PresenceType::class, $presence);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->presenceRepository->flush();

            $this->dispatcher->dispatch(new PresenceUpdated($presence->getId()));

            return $this->redirectToRoute('edr_admin_presence_show', [
                'id' => $presence->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/presence/edit.html.twig',
            [
                'presence' => $presence,
                'form' => $form,
            ]
        );
    }

    #[Route(path: '/{id}/delete', name: 'edr_admin_presence_delete', methods: ['POST'])]
    public function delete(Request $request, Presence $presence): RedirectResponse
    {
        $enfant = $presence->getEnfant();
        if ($this->isCsrfTokenValid('delete'.$presence->getId(), $request->request->get('_token'))) {
            if ($this->factureHandler->isBilled($presence->getId(), FactureInterface::OBJECT_PRESENCE)) {
                $this->addFlash('danger', 'Une présence déjà facturée ne peut être supprimée');

                return $this->redirectToRoute('edr_admin_presence_show', [
                    'id' => $presence->getId(),
                ]);
            }

            $presenceId = $presence->getId();
            $this->presenceRepository->remove($presence);
            $this->presenceRepository->flush();
            $this->dispatcher->dispatch(new PresenceDeleted($presenceId));
        }

        return $this->redirectToRoute('edr_admin_enfant_show', [
            'id' => $enfant->getId(),
        ]);
    }
}
