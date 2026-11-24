<?php

namespace AcMarche\Edr\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait IsAccueilEcoleTrait
{
    /**
     * @var bool
     */
    #[ORM\Column(type: 'boolean')]
    private bool $accueil_ecole = false;

    public function isAccueilEcole(): bool
    {
        return $this->accueil_ecole;
    }

    public function setAccueilEcole(bool $accueil_ecole): void
    {
        $this->accueil_ecole = $accueil_ecole;
    }
}
