<?php

namespace AcMarche\Edr\Entity\Security\Traits;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

trait LastLoginTrait
{
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $last_login = null;

    public function getLastLogin(): ?DateTimeImmutable
    {
        return $this->last_login;
    }

    public function setLastLogin(?DateTimeImmutable $last_login): void
    {
        $this->last_login = $last_login;
    }
}
