<?php

namespace AcMarche\Edr\Entity\Plaine;

use Doctrine\ORM\Mapping as ORM;

trait PrematernelleTrait
{
    #[ORM\Column(type: 'boolean', nullable: true)]
    private bool $prematernelle = false;

    public function getPrematernelle(): bool
    {
        return $this->prematernelle;
    }

    public function setPrematernelle(bool $prematernelle): void
    {
        $this->prematernelle = $prematernelle;
    }
}
