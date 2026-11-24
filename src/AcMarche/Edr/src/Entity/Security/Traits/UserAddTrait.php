<?php

namespace AcMarche\Edr\Entity\Security\Traits;

use Doctrine\ORM\Mapping as ORM;

trait UserAddTrait
{
    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $userAdd = null;

    public function getUserAdd(): ?string
    {
        return $this->userAdd;
    }

    public function setUserAdd(?string $userAdd): void
    {
        $this->userAdd = $userAdd;
    }
}
