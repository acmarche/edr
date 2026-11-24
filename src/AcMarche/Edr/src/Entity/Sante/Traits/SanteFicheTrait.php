<?php

namespace AcMarche\Edr\Entity\Sante\Traits;

use AcMarche\Edr\Entity\Sante\SanteFiche;
use Doctrine\ORM\Mapping as ORM;

trait SanteFicheTrait
{
    #[ORM\OneToOne(targetEntity: SanteFiche::class, mappedBy: 'enfant', cascade: ['remove'])]
    private ?SanteFiche $sante_fiche = null;

    public function getSanteFiche(): ?SanteFiche
    {
        return $this->sante_fiche;
    }

    public function setSanteFiche(?SanteFiche $sante_fiche): void
    {
        $this->sante_fiche = $sante_fiche;
    }
}
