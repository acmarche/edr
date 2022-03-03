<?php

namespace AcMarche\Edr\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait PrixTrait
{
    #[ORM\Column(type: 'decimal', precision: 4, scale: 2, nullable: false)]
    #[Assert\NotBlank]
    private float $prix1;

    #[ORM\Column(type: 'decimal', precision: 4, scale: 2, nullable: false)]
    #[Assert\NotBlank]
    private float $prix2;

    #[ORM\Column(type: 'decimal', precision: 4, scale: 2, nullable: false)]
    #[Assert\NotBlank]
    private float $prix3;

    public function getPrix1(): float
    {
        return $this->prix1;
    }

    public function setPrix1(float $prix1): void
    {
        $this->prix1 = $prix1;
    }

    public function getPrix2(): float
    {
        return $this->prix2;
    }

    public function setPrix2(float $prix2): void
    {
        $this->prix2 = $prix2;
    }

    public function getPrix3(): float
    {
        return $this->prix3;
    }

    public function setPrix3(float $prix3): void
    {
        $this->prix3 = $prix3;
    }
}
