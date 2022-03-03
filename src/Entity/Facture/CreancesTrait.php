<?php

namespace AcMarche\Edr\Entity\Facture;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

trait CreancesTrait
{
    /**
     * @var Collection|Creance[]
     */
    #[ORM\OneToMany(targetEntity: Creance::class, mappedBy: 'tuteur', cascade: ['remove'])]
    private Collection $creances;

    /**
     * @return Creance[]|Collection
     */
    public function getCreances(): Collection
    {
        return $this->creances;
    }

    /**
     * @param Creance[]|Collection $creances
     */
    public function setCreances(array|Collection $creances): void
    {
        $this->creances = $creances;
    }
}
