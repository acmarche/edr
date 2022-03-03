<?php

namespace AcMarche\Edr\Entity\Security\Traits;

use AcMarche\Edr\Entity\Security\User;
use Doctrine\Common\Collections\Collection;

trait UsersTrait
{
    /**
     * @var User[]|Collection
     */
    private Collection $users;

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }
}
