<?php

namespace AcMarche\Edr\Presence\Dto;

use AcMarche\Edr\Entity\Enfant;
use AcMarche\Edr\Entity\Jour;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Pour le listing par mois
 * Class JourListing.
 */
final class JourListing
{
    private ArrayCollection $enfants;

    public function __construct(
        private Jour $jour,
        array $enfants
    ) {
        $this->enfants = new ArrayCollection($enfants);
    }

    public function hasEnfant(Enfant $enfant): bool
    {
        return $this->enfants->contains($enfant);
    }

    public function getEnfants(): ArrayCollection
    {
        return $this->enfants;
    }

    public function setEnfants(ArrayCollection $arrayCollection): void
    {
        $this->enfants = $arrayCollection;
    }

    public function getJour(): Jour
    {
        return $this->jour;
    }

    public function setJour(Jour $jour): void
    {
        $this->jour = $jour;
    }
}
