<?php

namespace AcMarche\Edr\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait OrdreTrait
{
    #[ORM\Column(type: 'smallint', length: 2, nullable: false)]
    #[Assert\NotBlank]
    private int $ordre = 0;

    public function getOrdre(): int
    {
        return $this->ordre;
    }

    public function setOrdre(int $ordre): void
    {
        $this->ordre = $ordre;
    }
}
