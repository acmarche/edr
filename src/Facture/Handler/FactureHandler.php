<?php

namespace AcMarche\Edr\Facture\Handler;

use AcMarche\Edr\Contrat\Facture\FactureHandlerInterface;
use AcMarche\Edr\Contrat\Presence\PresenceCalculatorInterface;
use AcMarche\Edr\Contrat\Presence\PresenceInterface;
use AcMarche\Edr\Entity\Facture\Facture;
use AcMarche\Edr\Entity\Facture\FactureComplement;
use AcMarche\Edr\Entity\Facture\FacturePresence;

use AcMarche\Edr\Entity\Presence\Presence;
use AcMarche\Edr\Entity\Scolaire\Ecole;
use AcMarche\Edr\Entity\Tuteur;
use AcMarche\Edr\Facture\Factory\CommunicationFactoryInterface;
use AcMarche\Edr\Facture\Factory\FactureFactory;
use AcMarche\Edr\Facture\FactureInterface;
use AcMarche\Edr\Facture\Repository\FacturePresenceNonPayeRepository;
use AcMarche\Edr\Facture\Repository\FacturePresenceRepository;
use AcMarche\Edr\Facture\Repository\FactureRepository;
use AcMarche\Edr\Presence\Repository\PresenceRepository;
use AcMarche\Edr\Tuteur\Repository\TuteurRepository;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use DateTime;
use DateTimeInterface;

final readonly class FactureHandler implements FactureHandlerInterface
{
    public function __construct(
        private FactureRepository $factureRepository,
        private FacturePresenceRepository $facturePresenceRepository,
        private FactureFactory $factureFactory,
        private PresenceCalculatorInterface $presenceCalculator,
        private PresenceRepository $presenceRepository,
        private TuteurRepository $tuteurRepository,
        private CommunicationFactoryInterface $communicationFactory,
        private FacturePresenceNonPayeRepository $facturePresenceNonPayeRepository
    ) {
    }

    public function newFacture(Tuteur $tuteur): FactureInterface
    {
        return $this->factureFactory->newInstance($tuteur);
    }

    /**
     * @param Facture     $facture
     * @param array|int[] $presencesId
     * @param array|int[] $accueilsId
     */
    public function handleManually(FactureInterface $facture, array $presencesId, array $accueilsId): Facture
    {
        $presences = $this->presenceRepository->findBy([
            'id' => $presencesId,
        ]);

        $this->finish($facture, $presences,[]);
        $this->flush();
        $facture->setCommunication($this->communicationFactory->generateForPresence($facture));
        $this->flush();

        return $facture;
    }

    public function generateByMonthForTuteur(Tuteur $tuteur, string $month): ?FactureInterface
    {
        [$month, $year] = explode('-', $month);
        $date = Carbon::createFromDate($year, $month, 01);

        $facture = $this->handleByTuteur($tuteur, $date);
        if ($facture instanceof Facture) {
            $this->flush();
            $facture->setCommunication($this->communicationFactory->generateForPresence($facture));
            $this->flush();
        }

        return $facture;
    }

    public function generateByMonthForEveryone(string $monthSelected): array
    {
        [$month, $year] = explode('-', $monthSelected);
        $date = Carbon::createFromDate($year, $month, 01);
        $factures = [];

        $tuteurs = $this->tuteurRepository->findAllOrderByNom();
        foreach ($tuteurs as $tuteur) {
            if (($facture = $this->handleByTuteur($tuteur, $date)) instanceof Facture) {
                $factures[] = $facture;
            }
        }

        $this->flush();
        foreach ($factures as $facture) {
            $facture->setCommunication($this->communicationFactory->generateForPresence($facture));
        }

        $this->flush();

        return $factures;
    }

    public function isBilled(int $presenceId, string $type): bool
    {
        return (bool) $this->facturePresenceRepository->findByIdAndType($presenceId, $type);
    }

    public function isSended(int $presenceId, string $type): bool
    {
        if (($facturePresence = $this->facturePresenceRepository->findByIdAndType($presenceId, $type)) instanceof FacturePresence) {
            return $facturePresence->getFacture()->getEnvoyeLe() instanceof DateTimeInterface;
        }

        return false;
    }

    public function registerDataOnFacturePresence(
        FactureInterface $facture,
        PresenceInterface $presence,
        FacturePresence $facturePresence
    ): void {
        $facturePresence->setPedagogique($presence->getJour()->isPedagogique());
        $facturePresence->setPresenceDate($presence->getJour()->getDateJour());

        $enfant = $presence->getEnfant();
        if (($ecole = $enfant->getEcole()) instanceof Ecole) {
            $facture->ecolesListing[$ecole->getId()] = $ecole;
        }

        $facturePresence->setNom($enfant->getNom());
        $facturePresence->setPrenom($enfant->getPrenom());

        $ordre = $this->presenceCalculator->getOrdreOnPresence($presence);
        $facturePresence->setOrdre($ordre);
        $facturePresence->setAbsent($presence->getAbsent());
        $facturePresence->setCoutBrut($this->presenceCalculator->getPrixByOrdre($presence, $ordre));
    }

    private function handleByTuteur(Tuteur $tuteur, CarbonInterface $date): ?Facture
    {
        $facture = $this->newFacture($tuteur);
        $facture->setMois($date->format('m-Y'));

        $presences = $this->facturePresenceNonPayeRepository->findPresencesNonPayes($tuteur, $date->toDateTime());


        if ([] === $presences) {
            return null;
        }

        $this->finish($facture, $presences, []);

        return $facture;
    }

    /**
     * @param array|Presence[] $presences
     */
    private function finish(Facture $facture, array $presences, array $accueils): Facture
    {
        $this->attachPresences($facture, $presences);
        $this->attachRetard($facture, $accueils);
        $this->factureFactory->setEcoles($facture);

        if (!$facture->getId()) {
            $this->factureRepository->persist($facture);
        }

        return $facture;
    }

    /**
     * @param array|Presence[] $presences
     */
    private function attachPresences(Facture $facture, array $presences): void
    {
        foreach ($presences as $presence) {
            $facturePresence = new FacturePresence($facture, $presence->getId(), FactureInterface::OBJECT_PRESENCE);
            $this->registerDataOnFacturePresence($facture, $presence, $facturePresence);
            $facturePresence->setCoutCalculated($this->presenceCalculator->calculate($presence));
            $this->facturePresenceRepository->persist($facturePresence);
            $facture->addFacturePresence($facturePresence);
        }
    }

    private function flush(): void
    {
        $this->factureRepository->flush();
        $this->facturePresenceRepository->flush();
    }

    /**
     * @param array|Accueil[] $accueils
     */
    private function attachRetard(Facture $facture, array $accueils): void
    {
        $retards = [];
        $total = 0;
        foreach ($accueils as $accueil) {
            if ($accueil->getHeureRetard() instanceof DateTimeInterface) {
                $total += $this->accueilCalculator->calculateRetard($accueil);
                $retards[] = $accueil->getDateJour()->format('d-m');
            }
        }

        if ($total > 0) {
            $complement = new FactureComplement($facture);
            $complement->setDateLe(new DateTime());
            $complement->setForfait($total);
            $complement->setNom('Retard pour les accueils: ' . implode(', ', $retards));
            $facture->addFactureComplement($complement);
            $this->factureRepository->persist($complement);
        }
    }
}
