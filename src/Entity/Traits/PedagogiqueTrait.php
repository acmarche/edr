<?php

namespace AcMarche\Edr\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait PedagogiqueTrait
{
    #[ORM\Column(type: 'boolean')]
    private bool $pedagogique = false;

    public function isPedagogique(): bool
    {
        return $this->pedagogique;
    }

    public function setPedagogique(bool $pedagogique): void
    {
        $this->pedagogique = $pedagogique;
    }
}
