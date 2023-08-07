<?php

namespace AcMarche\Edr\Plaine\Handler;

use AcMarche\Edr\Contrat\Plaine\PlaineHandlerInterface;
use AcMarche\Edr\Contrat\Presence\PresenceHandlerInterface;
use AcMarche\Edr\Entity\Enfant;
use AcMarche\Edr\Entity\Plaine\Plaine;
use AcMarche\Edr\Entity\Presence\Presence;
use AcMarche\Edr\Entity\Tuteur;
use AcMarche\Edr\Facture\Handler\FacturePlaineHandler;
use AcMarche\Edr\Mailer\Factory\AdminEmailFactory;
use AcMarche\Edr\Mailer\Factory\FactureEmailFactory;
use AcMarche\Edr\Mailer\NotificationMailer;
use AcMarche\Edr\Plaine\Repository\PlainePresenceRepository;
use AcMarche\Edr\Tuteur\Utils\TuteurUtils;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Exception;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class PlaineHandlerHotton implements PlaineHandlerInterface
{
    public function __construct(
        private readonly PlainePresenceRepository $plainePresenceRepository,
        private readonly FacturePlaineHandler $facturePlaineHandler,
        private readonly FactureEmailFactory $factureEmailFactory,
        private readonly NotificationMailer $notificationMailer,
        private readonly AdminEmailFactory $adminEmailFactory,
        private readonly PresenceHandlerInterface $presenceHandler
    ) {
    }

    public function handleAddEnfant(Plaine $plaine, Tuteur $tuteur, Enfant $enfant): void
    {
        $jours = $plaine->getJours();
        $this->presenceHandler->handleNew($tuteur, $enfant, $jours);
    }

    public function handleEditPresences(
        Tuteur $tuteur,
        Enfant $enfant,
        array $currentJours,
        Collection $collection
    ): void {
        $enMoins = array_diff($currentJours, $collection->toArray());
        $enPlus = array_diff($collection->toArray(), $currentJours);

        foreach ($enPlus as $jour) {
            $presence = new Presence($tuteur, $enfant, $jour);
            $this->plainePresenceRepository->persist($presence);
        }

        foreach ($enMoins as $jour) {
            $presence = $this->plainePresenceRepository->findOneByEnfantJour($enfant, $jour);
            if ($presence instanceof Presence) {
                $this->plainePresenceRepository->remove($presence);
            }
        }

        $this->plainePresenceRepository->flush();
    }

    public function removeEnfant(Plaine $plaine, Enfant $enfant): void
    {
        $presences = $this->plainePresenceRepository->findByPlaineAndEnfant($plaine, $enfant);
        foreach ($presences as $presence) {
            $this->plainePresenceRepository->remove($presence);
        }

        $this->plainePresenceRepository->flush();
    }

    public function isRegistrationFinalized(Plaine $plaine, Tuteur $tuteur): bool
    {
        return [] !== $this->plainePresenceRepository->findByPlaineAndTuteur($plaine, $tuteur, true);
    }

    /**
     * @throws Exception
     */
    public function confirm(Plaine $plaine, Tuteur $tuteur): void
    {
        $inscriptions = $this->plainePresenceRepository->findByPlaineAndTuteur($plaine, $tuteur);
        foreach ($inscriptions as $inscription) {
            $inscription->setConfirmed(true);
        }

        $this->plainePresenceRepository->flush();

        $facture = $this->facturePlaineHandler->newInstance($plaine, $tuteur);
        $this->plainePresenceRepository->persist($facture);
        $this->plainePresenceRepository->flush();

        $this->facturePlaineHandler->handleManually($facture, $plaine);

        $emails = TuteurUtils::getEmailsOfOneTuteur($tuteur);
        if (\count($emails) < 1) {
            $error = 'Pas de mail pour la facture plaine: ' . $facture->getId();
            $message = $this->adminEmailFactory->messageAlert('Erreur envoie facture', $error);
            $this->notificationMailer->sendAsEmailNotification($message);
            throw new Exception($error);
        }

        $from = $this->factureEmailFactory->getEmailAddressOrganisation();
        $message = $this->factureEmailFactory->messageFacture($from, 'Inscription à la plaine', 'Coucou');
        $this->factureEmailFactory->setTos($message, $emails);
        $this->factureEmailFactory->attachFactureOnTheFly($facture, $message);

        try {
            $this->notificationMailer->sendMail($message);
        } catch (TransportExceptionInterface $transportException) {
            $error = 'Facture plaine num ' . $facture->getId() . ' ' . $transportException->getMessage();
            $message = $this->adminEmailFactory->messageAlert('Erreur envoie facture plaine', $error);
            $this->notificationMailer->sendAsEmailNotification($message);
        }

        $this->notificationMailer->sendAsEmailNotification($message);
        $facture->setEnvoyeA(implode(',', $emails));
        $facture->setEnvoyeLe(new DateTime());

        $this->plainePresenceRepository->flush();
    }
}
