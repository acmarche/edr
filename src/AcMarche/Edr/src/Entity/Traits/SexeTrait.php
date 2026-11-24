<?php

namespace AcMarche\Edr\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait SexeTrait
{
    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 150, nullable: true)]
    private ?string $sexe = null;

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(?string $sexe): void
    {
        $this->sexe = $sexe;
    }
}
