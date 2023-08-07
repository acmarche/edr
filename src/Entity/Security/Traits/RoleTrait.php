<?php

namespace AcMarche\Edr\Entity\Security\Traits;

use Doctrine\ORM\Mapping as ORM;

trait RoleTrait
{
    #[ORM\Column(type: 'array')]
    private array $roles = [];

    public function addRole(string $role): void
    {
        if (!\in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }
    }

    public function removeRole(string $role): void
    {
        if (\in_array($role, $this->roles, true)) {
            $index = array_search($role, $this->roles, true);
            unset($this->roles[$index]);
        }
    }

    public function hasRole(string $role): bool
    {
        return \in_array($role, $this->getRoles(), true);
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }
}
