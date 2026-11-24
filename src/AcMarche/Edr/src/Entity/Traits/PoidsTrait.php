<?php

namespace AcMarche\Edr\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait PoidsTrait
{
    #[ORM\Column(type: 'string', length: 150, nullable: true)]
    private ?string $poids = null;

    public function getPoids(): ?string
    {
        return $this->poids;
    }

    public function setPoids(string $prenom): void
    {
        $this->poids = $prenom;
    }
}
