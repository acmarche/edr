<?php

namespace AcMarche\Edr\Entity;

use AcMarche\Edr\Entity\Traits\IdTrait;
use AcMarche\Edr\Entity\Traits\NomTrait;
use AcMarche\Edr\Entity\Traits\RemarqueTrait;
use AcMarche\Edr\Reduction\Repository\ReductionRepository;
use AcMarche\Edr\Reduction\Validator as AcMarcheReductionAssert;
use Doctrine\ORM\Mapping as ORM;
use Stringable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @AcMarcheReductionAssert\PourcentageOrForfait()
 */
#[ORM\Entity(repositoryClass: ReductionRepository::class)]
class Reduction implements Stringable
{
    use IdTrait;
    use NomTrait;
    use RemarqueTrait;
    #[ORM\Column(type: 'float', nullable: true)]
    #[Assert\Range(min: 0, max: 100)]
    private ?float $pourcentage = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Assert\Range(min: 0)]
    private ?float $forfait = null;

    public function __toString(): string
    {
        return $this->getNom() . ' (' . $this->pourcentage . '%)';
    }

    public function getPourcentage(): ?float
    {
        return $this->pourcentage;
    }

    public function setPourcentage(?float $pourcentage): self
    {
        $this->pourcentage = $pourcentage;

        return $this;
    }

    public function getForfait(): ?float
    {
        return $this->forfait;
    }

    public function setForfait(?float $forfait): self
    {
        $this->forfait = $forfait;

        return $this;
    }
}
