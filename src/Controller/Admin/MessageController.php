<?php

namespace AcMarche\Edr\Controller\Admin;

use AcMarche\Edr\Contrat\Presence\PresenceHandlerInterface;
use AcMarche\Edr\Entity\Jour;
use AcMarche\Edr\Entity\Message;
use AcMarche\Edr\Entity\Plaine\Plaine;
use AcMarche\Edr\Entity\Scolaire\GroupeScolaire;
use AcMarche\Edr\Message\Factory\MessageFactory;
use AcMarche\Edr\Message\Form\MessagePlaineType;
use AcMarche\Edr\Message\Form\MessageType;
use AcMarche\Edr\Message\Form\SearchMessageType;
use AcMarche\Edr\Message\Handler\MessageHandler;
use AcMarche\Edr\Message\Repository\MessageRepository;
use AcMarche\Edr\Plaine\Repository\PlainePresenceRepository;
use AcMarche\Edr\Presence\Repository\PresenceRepository;
use AcMarche\Edr\Presence\Utils\PresenceUtils;
use AcMarche\Edr\Relation\Repository\RelationRepository;
use AcMarche\Edr\Relation\Utils\RelationUtils;
use AcMarche\Edr\Search\SearchHelper;
use AcMarche\Edr\Tuteur\Utils\TuteurUtils;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[IsGranted('ROLE_MERCREDI_ADMIN')]
#[Route(path: '/message')]
final class MessageController extends AbstractController
{
    public function __construct(
        private readonly PresenceRepository $presenceRepository,
        private readonly PlainePresenceRepository $plainePresenceRepository,
        private readonly RelationRepository $relationRepository,
        private readonly MessageRepository $messageRepository,
        private readonly SearchHelper $searchHelper,
        private readonly TuteurUtils $tuteurUtils,
        private readonly MessageFactory $messageFactory,
        private readonly MessageHandler $messageHandler,
        private readonly PresenceHandlerInterface $presenceHandler
    ) {
    }

    #[Route(path: '/', name: 'edr_message_index')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(SearchMessageType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $ecole = $data['ecole'];
            $jour = $data['jour'];
            $plaine = $data['plaine'];
            $tuteurs = [[]];

            if ($jour) {
                $presences = $this->presenceRepository->findByDay($jour);
                $tuteurs[] = PresenceUtils::extractTuteurs($presences);
            }

            if ($ecole) {
                $relations = $this->relationRepository->findByEcole($ecole);
                $tuteurs[] = RelationUtils::extractTuteurs($relations);
            }

            if ($plaine) {
                $presences = $this->plainePresenceRepository->findByPlaine($plaine);
                $tuteurs[] = PresenceUtils::extractTuteurs($presences);
            }

            if (!$jour && !$ecole && !$plaine) {
                $relations = $this->relationRepository->findTuteursActifs();
                $tuteurs[] = RelationUtils::extractTuteurs($relations);
            }

            $tuteurs = array_merge(...$tuteurs);
        } else {
            $relations = $this->relationRepository->findTuteursActifs();
            $tuteurs = RelationUtils::extractTuteurs($relations);
        }

        $emails = $this->tuteurUtils->getEmails($tuteurs);
        $tuteursWithOutEmails = $this->tuteurUtils->filterTuteursWithOutEmail($tuteurs);
        $this->searchHelper->saveSearch(SearchHelper::MESSAGE_INDEX, $emails);

        return $this->render(
            '@AcMarcheEdrAdmin/message/index.html.twig',
            [
                'form' => $form,
                'emails' => $emails,
                'tuteurs' => $tuteursWithOutEmails,
            ]
        );
    }

    #[Route(path: '/jour/{id}', name: 'edr_message_new_jour')]
    public function fromJour(Request $request, Jour $jour): Response
    {
        $presences = $this->presenceRepository->findByDay($jour);
        $tuteurs = PresenceUtils::extractTuteurs($presences);
        $emails = $this->tuteurUtils->getEmails($tuteurs);
        $message = $this->messageFactory->createInstance();
        $message->setDestinataires($emails);

        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->messageHandler->handle($message);

            $this->addFlash('success', 'Le message a bien été envoyé');

            return $this->redirectToRoute('edr_message_index');
        }

        return $this->render(
            '@AcMarcheEdrAdmin/message/new.html.twig',
            [
                'emailuser' => $this->getUser()->getEmail(),
                'form' => $form,
                'emails' => $emails,
                'jour' => $jour,
                'tuteurs' => [],
            ]
        );
    }

    #[Route(path: '/groupe/{id}', name: 'edr_message_new_groupescolaire')]
    public function fromGroupeScolaire(Request $request, GroupeScolaire $groupeScolaire): Response
    {
        $args = $this->searchHelper->getArgs(SearchHelper::PRESENCE_LIST);
        if (\count($args) < 1) {
            $this->addFlash('danger', 'Aucun critère de recherche encodé');

            return $this->redirectToRoute('edr_admin_presence_index');
        }

        $jour = $args['jour'];
        $ecole = $args['ecole'];
        $data = $this->presenceHandler->searchAndGrouping($jour, $ecole, false);
        $enfants = $data[$groupeScolaire->getId()]['enfants'] ?? [];
        $tuteurs = $this->tuteurUtils->getTuteursByEnfants($enfants);
        $emails = $this->tuteurUtils->getEmails($tuteurs);
        $message = $this->messageFactory->createInstance();
        $message->setDestinataires($emails);

        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->messageHandler->handle($message);

            $this->addFlash('success', 'Le message a bien été envoyé');

            return $this->redirectToRoute('edr_admin_presence_index');
        }

        return $this->render(
            '@AcMarcheEdrAdmin/message/new.html.twig',
            [
                'emailuser' => $this->getUser()->getEmail(),
                'form' => $form,
                'emails' => $emails,
                'tuteurs' => [],
            ]
        );
    }

    #[Route(path: '/plaine/{id}', name: 'edr_message_new_plaine')]
    public function fromPlaine(Request $request, Plaine $plaine): Response
    {
        $presences = $this->plainePresenceRepository->findByPlaine($plaine);
        $tuteurs = PresenceUtils::extractTuteurs($presences);
        $emails = $this->tuteurUtils->getEmails($tuteurs);
        $message = $this->messageFactory->createInstance();
        $message->setDestinataires($emails);

        $form = $this->createForm(MessagePlaineType::class, $message);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $attachCourrier = (bool) $form->get('attachCourriers')->getData();
            $this->messageHandler->handleFromPlaine($plaine, $message, $attachCourrier);

            $this->addFlash('success', 'Le message a bien été envoyé');

            return $this->redirectToRoute('edr_message_index');
        }

        return $this->render(
            '@AcMarcheEdrAdmin/message/new_from_plaine.html.twig',
            [
                'emailuser' => $this->getUser()->getEmail(),
                'form' => $form,
                'emails' => $emails,
                'plaine' => $plaine,
                'tuteurs' => [],
            ]
        );
    }

    #[Route(path: '/new', name: 'edr_message_new')]
    public function new(Request $request): Response
    {
        $emails = $this->searchHelper->getArgs(SearchHelper::MESSAGE_INDEX);
        $message = $this->messageFactory->createInstance();
        $message->setDestinataires($emails);

        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->messageHandler->handle($message);

            $this->addFlash('success', 'Le message a bien été envoyé');

            $this->searchHelper->deleteSearch(SearchHelper::MESSAGE_INDEX);

            return $this->redirectToRoute('edr_message_index');
        }

        return $this->render(
            '@AcMarcheEdrAdmin/message/new.html.twig',
            [
                'emails' => $emails,
                'form' => $form,
            ]
        );
    }

    #[Route(path: 'archive', name: 'edr_message_archive')]
    public function archive(): Response
    {
        $messages = $this->messageRepository->findall();

        return $this->render(
            '@AcMarcheEdr/admin/message/archive.html.twig',
            [
                'messages' => $messages,
            ]
        );
    }

    #[Route(path: '/show/{id}', name: 'edr_message_show', methods: ['GET'])]
    public function show(Message $message): Response
    {
        return $this->render(
            '@AcMarcheEdr/admin/message/show.html.twig',
            [
                'message' => $message,
            ]
        );
    }
}
