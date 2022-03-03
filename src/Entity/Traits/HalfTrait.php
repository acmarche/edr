<?php

namespace AcMarche\Edr\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait HalfTrait
{
    /**
     * @var bool
     */
    #[ORM\Column(type: 'boolean')]
    private bool $half = false;

    public function isHalf(): bool
    {
        return $this->half;
    }

    public function setHalf(bool $half): void
    {
        $this->half = $half;
    }
}
