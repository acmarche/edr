<?php

namespace AcMarche\Edr\User\Dto;

use AcMarche\Edr\Entity\Scolaire\Ecole;
use AcMarche\Edr\Entity\Security\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface;

final class AssociateUserEcoleDto
{
    /**
     * @var Ecole[]|ArrayCollection
     */
    private Collection $ecoles;

    private bool $sendEmail = true;

    public function __construct(
        private readonly UserInterface $user
    ) {
        $this->ecoles = new ArrayCollection();
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function isSendEmail(): bool
    {
        return $this->sendEmail;
    }

    public function setSendEmail(bool $sendEmail): void
    {
        $this->sendEmail = $sendEmail;
    }

    /**
     * @return Collection|Ecole[]
     */
    public function getEcoles(): Collection
    {
        return $this->ecoles;
    }

    public function addEcole(Ecole $ecole): self
    {
        if (!$this->ecoles->contains($ecole)) {
            $this->ecoles[] = $ecole;
        }

        return $this;
    }

    public function removeEcole(Ecole $ecole): self
    {
        if ($this->ecoles->contains($ecole)) {
            $this->ecoles->removeElement($ecole);
        }

        return $this;
    }
}
