<?php

namespace AcMarche\Edr\Controller\Ecole;

use AcMarche\Edr\Accueil\Calculator\AccueilCalculatorInterface;
use AcMarche\Edr\Accueil\Contrat\AccueilInterface;
use AcMarche\Edr\Accueil\Form\AccueilRetardTpe;
use AcMarche\Edr\Accueil\Form\AccueilType;
use AcMarche\Edr\Accueil\Form\InscriptionsType;
use AcMarche\Edr\Accueil\Form\SearchAccueilByDate;
use AcMarche\Edr\Accueil\Handler\AccueilHandler;
use AcMarche\Edr\Accueil\Message\AccueilCreated;
use AcMarche\Edr\Accueil\Message\AccueilUpdated;
use AcMarche\Edr\Accueil\Repository\AccueilRepository;
use AcMarche\Edr\Enfant\Repository\EnfantRepository;
use AcMarche\Edr\Entity\Enfant;
use AcMarche\Edr\Entity\Presence\Accueil;
use AcMarche\Edr\Entity\Scolaire\Ecole;
use AcMarche\Edr\Entity\Tuteur;
use AcMarche\Edr\Facture\Repository\FacturePresenceRepository;
use AcMarche\Edr\Relation\Repository\RelationRepository;
use AcMarche\Edr\Utils\DateUtils;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/accueil')]
#[IsGranted(data: 'ROLE_MERCREDI_ECOLE')]
final class AccueilController extends AbstractController
{
    use GetEcolesTrait;

    public function __construct(
        private readonly AccueilRepository $accueilRepository,
        private readonly AccueilHandler $accueilHandler,
        private readonly RelationRepository $relationRepository,
        private readonly AccueilCalculatorInterface $accueilCalculator,
        private readonly EnfantRepository $enfantRepository,
        private readonly DateUtils $dateUtils,
        private readonly FacturePresenceRepository $facturePresenceRepository,
        private readonly MessageBusInterface $dispatcher
    ) {
    }

    #[Route(path: '/index', name: 'edr_ecole_accueils_index', methods: ['GET', 'POST'])]
    #[IsGranted(data: 'ROLE_MERCREDI_ECOLE')]
    public function index(Request $request): Response
    {
        if (($response = $this->hasEcoles()) instanceof Response) {
            return $response;
        }

        $accueils = [];
        $form = $this->createForm(SearchAccueilByDate::class, []);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $date = $data['date_jour'];
            $heure = $data['heure'];
            $ecoles = $this->ecoles;
            $accueils = $this->accueilRepository->findByDateAndHeureAndEcoles($date, $heure, $ecoles);
        }

        return $this->render(
            '@AcMarcheEdrEcole/accueil/index.html.twig',
            [
                'accueils' => $accueils,
                'form' => $form->createView(),
                'search' => $form->isSubmitted(),
            ]
        );
    }

    #[Route(path: '/new/{tuteur}/{enfant}', name: 'edr_ecole_accueil_new', methods: ['GET', 'POST'])]
    #[Entity(data: 'tuteur', expr: 'repository.find(tuteur)')]
    #[Entity(data: 'enfant', expr: 'repository.find(enfant)')]
    #[IsGranted(data: 'enfant_edit', subject: 'enfant')]
    public function new(Request $request, Tuteur $tuteur, Enfant $enfant): Response
    {
        $accueil = new Accueil($tuteur, $enfant);
        $form = $this->createForm(AccueilType::class, $accueil);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $result = $this->accueilHandler->handleNew($enfant, $accueil);
            $this->dispatcher->dispatch(new AccueilCreated($result->getId()));

            return $this->redirectToRoute('edr_ecole_accueil_show', [
                'uuid' => $result->getUuid(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrEcole/accueil/new.html.twig',
            [
                'enfant' => $enfant,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{uuid}/show', name: 'edr_ecole_accueil_show', methods: ['GET'])]
    #[IsGranted(data: 'accueil_show', subject: 'accueil')]
    public function show(Accueil $accueil): Response
    {
        $enfant = $accueil->getEnfant();
        $cout = $this->accueilCalculator->calculate($accueil);
        $factureAccueil = $this->facturePresenceRepository->findByAccueil($accueil);

        return $this->render(
            '@AcMarcheEdrEcole/accueil/show.html.twig',
            [
                'accueil' => $accueil,
                'cout' => $cout,
                'enfant' => $enfant,
                'facture' => $factureAccueil,
            ]
        );
    }

    #[Route(path: '/{uuid}/edit', name: 'edr_ecole_accueil_edit', methods: ['GET', 'POST'])]
    #[IsGranted(data: 'accueil_edit', subject: 'accueil')]
    public function edit(Request $request, Accueil $accueil): Response
    {
        $form = $this->createForm(AccueilType::class, $accueil);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->accueilRepository->flush();

            $this->dispatcher->dispatch(new AccueilUpdated($accueil->getId()));

            return $this->redirectToRoute('edr_ecole_accueil_show', [
                'uuid' => $accueil->getUuid(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrEcole/accueil/edit.html.twig',
            [
                'accueil' => $accueil,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/inscriptions/{id}/year/{year}/month/{month}/week/{week}/heure/{heure}', name: 'edr_ecole_accueil_inscriptions', methods: [
        'GET',
        'POST',
    ])]
    public function inscriptions(
        Request $request,
        Ecole $ecole,
        int $year,
        int $month,
        string $heure,
        int $week = 0
    ): Response {
        if (0 !== $week) {
            $date = $this->dateUtils->createDateImmutableFromYearWeek($year, $week);
        } else {
            //pas de week quand on change de mois
            $date = $this->dateUtils->createDateImmutableFromYearMonth($year, $month);
        }

        $weekPeriod = $this->dateUtils->getWeekByNumber($date, $week);
        $data = [];
        $enfants = $this->enfantRepository->findByEcolesForInscription($ecole);
        foreach ($enfants as $enfant) {
            $tuteurSelected = 0;
            $accueils = $this->accueilRepository->findByEnfantAndDaysAndHeure($enfant, $weekPeriod, $heure);
            $rows = [
                'accueils' => [],
                'tuteurs' => [],
            ];
            foreach ($accueils as $accueil) {
                $rows['accueils'][$accueil->getDateJour()->format('Y-m-d')] = [
                    'duree' => $accueil->getDuree(),
                    'tuteur' => $accueil->getTuteur()->getId(),
                ];
            //    $tuteurSelected = $accueil->getTuteur()->getId();
            }

            $rows['tuteurSelected'] = $tuteurSelected;
            $data[$enfant->getId()] = $rows;
        }

        $form = $this->createForm(InscriptionsType::class, $data);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $accueils = $request->request->all('accueils');
            $tuteurs = $request->request->all('tuteurs');

            $this->accueilHandler->handleCollections($accueils, $tuteurs, $heure);
            $this->addFlash('success', 'Les acceuils ont bien été sauvegardés');

            return $this->redirectToRoute('edr_ecole_accueil_inscriptions', [
                'id' => $ecole->getId(),
                'year' => $year,
                'month' => $month,
                'heure' => $heure,
                'week' => $week,
            ]);
        }

        $calendar = $this->dateUtils->renderMonth($ecole, $heure, $week, $date);

        return $this->render(
            '@AcMarcheEdrEcole/accueil/inscriptions.html.twig',
            [
                'ecole' => $ecole,
                'enfants' => $enfants,
                'week' => $weekPeriod,
                'date' => $date,
                'heure' => $heure,
                'form' => $form->createView(),
                'calendar' => $calendar,
                'data' => $data,
            ]
        );
    }

    #[Route(path: '/{uuid}/retard', name: 'edr_ecole_accueil_retard', methods: ['GET', 'POST'])]
    #[IsGranted(data: 'enfant_show', subject: 'enfant')]
    public function retard(Request $request, Enfant $enfant): Response
    {
        $args = [
            'date_retard' => new DateTime(),
            'heure_retard' => new DateTime(),
        ];
        $form = $this->createForm(AccueilRetardTpe::class, $args);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $dateRetard = $data['date_retard'];
            $heureRetard = $data['heure_retard'];
            if (!($accueil = $this->accueilRepository->findOneByEnfantAndDayAndHour(
                    $enfant,
                    $dateRetard,
                    AccueilInterface::SOIR
                )) instanceof Accueil) {
                $this->addFlash('danger', 'Aucun accueil encodé pour ce jour là. Veuillez d\'abord ajouté un accueil');
            } else {
                $dateRetard->setTime($heureRetard->format('H'), $heureRetard->format('i'));
                $accueil->setHeureRetard($dateRetard);
                $this->accueilRepository->flush();
                $this->addFlash('success', 'Le retard a bien été ajouté');
            }

            return $this->redirectToRoute('edr_ecole_enfant_show', [
                'uuid' => $enfant->getUuid(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrEcole/accueil/retard.html.twig',
            [
                'enfant' => $enfant,
                'form' => $form->createView(),
            ]
        );
    }
}
