<?php

namespace AcMarche\Edr\Presence\Dto;

use AcMarche\Edr\Entity\Enfant;
use AcMarche\Edr\Entity\Jour;
use AcMarche\Edr\Entity\Presence\Presence;
use AcMarche\Edr\Jour\Repository\JourRepository;
use AcMarche\Edr\Presence\Repository\PresenceRepository;
use DateTimeInterface;

final class ListingPresenceByMonth
{
    /**
     * @var Presence[]
     */
    private array $presences = [];

    /**
     * @var Enfant[]
     */
    private array $enfants = [];

    /**
     * @var JourListing[]
     */
    private array $joursListing = [];

    public function __construct(
        private readonly PresenceRepository $presenceRepository,
        private readonly JourRepository $jourRepository
    ) {
    }

    public function create(DateTimeInterface $dateTime): self
    {
        $daysOfMonth = $this->getDaysOfMonth($dateTime);
        $this->presences = $this->getPresencesOfMonth($dateTime);
        $this->enfants = $this->getEnfantsPresentsOfMonth();

        $joursListing = [];

        foreach ($daysOfMonth as $jour) {
            $presences = $this->presenceRepository->findByDay($jour);
            $enfantsByday = array_map(
                static fn($presence) => $presence->getEnfant(),
                $presences
            );
            $joursListing[] = new JourListing($jour, $enfantsByday);
        }

        $this->joursListing = $joursListing;

        return $this;
    }

    /**
     * @return Jour[]
     */
    public function getDaysOfMonth(DateTimeInterface $dateTime): array
    {
        return $this->jourRepository->findDaysByMonth($dateTime);
    }

    /**
     * @return Presence[]
     */
    public function getPresences(): array
    {
        return $this->presences;
    }

    /**
     * @return Enfant[]
     */
    public function getEnfants(): array
    {
        return $this->enfants;
    }

    /**
     * @return JourListing[]
     */
    public function getJoursListing(): array
    {
        return $this->joursListing;
    }

    /**
     * @return Presence[]
     */
    private function getPresencesOfMonth(DateTimeInterface $dateTime): array
    {
        $jours = $this->jourRepository->findDaysByMonth($dateTime);

        return $this->presenceRepository->findByDays($jours);
    }

    /**
     * @return Enfant[]
     */
    private function getEnfantsPresentsOfMonth(): array
    {
        $enfants = array_map(
            static fn($presence) => $presence->getEnfant(),
            $this->presences
        );

        return array_unique($enfants);
    }
}
