<?php

namespace AcMarche\Edr\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait RegistreNationalTrait
{
    #[ORM\Column(type: 'string', length: 150, nullable: true)]
    private ?string $registre_national = null;

    public function getRegistreNational(): ?string
    {
        return $this->registre_national;
    }

    public function setRegistreNational(string $prenom): void
    {
        $this->registre_national = $prenom;
    }
}
