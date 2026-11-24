<?php

namespace AcMarche\Edr\Entity\Security\Traits;

trait PlainPasswordTrait
{
    private ?string $plain_password = null;

    public function getPlainPassword(): ?string
    {
        return $this->plain_password;
    }

    public function setPlainPassword(?string $plain_password): void
    {
        $this->plain_password = $plain_password;
    }
}
