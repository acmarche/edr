<?php

namespace AcMarche\Edr\Accueil\Handler;

use AcMarche\Edr\Entity\Tuteur;
use AcMarche\Edr\Accueil\Repository\AccueilRepository;
use AcMarche\Edr\Enfant\Repository\EnfantRepository;
use AcMarche\Edr\Entity\Enfant;
use AcMarche\Edr\Entity\Presence\Accueil;
use AcMarche\Edr\Tuteur\Repository\TuteurRepository;
use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

final class AccueilHandler
{
    private readonly FlashBagInterface $flashBag;

    public function __construct(
        private readonly AccueilRepository $accueilRepository,
        private readonly EnfantRepository $enfantRepository,
        private readonly TuteurRepository $tuteurRepository,
        RequestStack $requestStack
    ) {
        $this->flashBag = $requestStack->getSession()->getFlashBag();
    }

    public function handleNew(Enfant $enfant, Accueil $accueilSubmited): Accueil
    {
        if (($accueil = $this->accueilRepository->isRegistered($accueilSubmited, $enfant)) instanceof Accueil) {
            $this->updateAccueil($accueil, $accueilSubmited);

            return $accueilSubmited;
        }

        if ($accueilSubmited->getDuree() > 0) {
            $this->accueilRepository->persist($accueilSubmited);
            $this->accueilRepository->flush();
        }

        return $accueilSubmited;
    }

    public function handleCollections(array $accueils, array $tuteurs, string $heure): void
    {
        foreach ($accueils as $enfantId => $days) {
            foreach ($days as $dateString => $duree) {
                $duree = (int) $duree;

                if (!($enfant = $this->enfantRepository->find((int) $enfantId)) instanceof Enfant) {
                    $this->flashBag->add('danger', 'Référence pour l\enfant '.$enfantId.' non trouvé');

                    continue;
                }

                $tuteurId = (int) $tuteurs[$enfantId][0];

                if (!($tuteur = $this->tuteurRepository->find($tuteurId)) instanceof Tuteur) {
                    if ($duree > 0) {
                        $this->flashBag->add('danger', 'Spécifié sous quelle garde pour '.$enfant);
                    }

                    continue;
                }

                $accueil = new Accueil($tuteur, $enfant);
                $accueil->setDuree($duree);
                $accueil->setHeure($heure);
                try {
                    $date = DateTime::createFromFormat('Y-m-d', $dateString);
                    $accueil->setDateJour($date);
                    $this->handleNew($enfant, $accueil);
                } catch (Exception $exception) {
                    $this->flashBag->add(
                        'danger',
                        'Impossible de convertir la date '.$dateString.' pour '.$enfant.': '.$exception->getMessage()
                    );

                    continue;
                }
            }
        }
    }

    public function handleOne(): void
    {
    }

    private function updateAccueil(Accueil $accueilExistant, Accueil $accueilSubmited): void
    {
        if (0 === $accueilSubmited->getDuree()) {
            $this->accueilRepository->remove($accueilExistant);
        } else {
            $accueilExistant->setHeure($accueilSubmited->getHeure());
            $accueilExistant->setDuree($accueilSubmited->getDuree());
            $accueilExistant->setRemarque($accueilSubmited->getRemarque());
        }

        $this->accueilRepository->flush();
    }
}
