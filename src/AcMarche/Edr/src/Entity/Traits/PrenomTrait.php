<?php

namespace AcMarche\Edr\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait PrenomTrait
{
    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $prenom = null;

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): void
    {
        $this->prenom = $prenom;
    }
}
