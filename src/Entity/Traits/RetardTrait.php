<?php

namespace AcMarche\Edr\Entity\Traits;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait RetardTrait
{
    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Assert\Type(type: 'datetime')]
    private ?DateTimeInterface $heure_retard = null;

    public function getHeureRetard(): ?DateTimeInterface
    {
        return $this->heure_retard;
    }

    public function setHeureRetard(?DateTimeInterface $heure_retard): void
    {
        $this->heure_retard = $heure_retard;
    }
}
