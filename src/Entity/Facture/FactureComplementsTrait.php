<?php

namespace AcMarche\Edr\Entity\Facture;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

trait FactureComplementsTrait
{
    /**
     * @var FactureComplement[]|Collection
     */
    #[ORM\OneToMany(targetEntity: FactureComplement::class, mappedBy: 'facture', cascade: ['remove'])]
    private Collection $factureComplements;

    /**
     * @return Collection|FactureComplement[]
     */
    public function getFactureComplements(): Collection
    {
        return $this->factureComplements;
    }

    public function addFactureComplement(FactureComplement $factureComplement): self
    {
        if (! $this->factureComplements->contains($factureComplement)) {
            $this->factureComplements[] = $factureComplement;
            $factureComplement->setFacture($this);
        }

        return $this;
    }

    public function removeFactureComplement(FactureComplement $factureComplement): self
    {
        if ($this->factureComplements->contains($factureComplement)) {
            $this->factureComplements->removeElement($factureComplement);
            // set the owning side to null (unless already changed)
            if ($factureComplement->getFacture() === $this) {
                $factureComplement->setFacture(null);
            }
        }

        return $this;
    }
}
