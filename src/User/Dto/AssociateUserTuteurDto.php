<?php

namespace AcMarche\Edr\User\Dto;

use AcMarche\Edr\Entity\Tuteur;
use Symfony\Component\Security\Core\User\UserInterface;

final class AssociateUserTuteurDto
{
    private ?Tuteur $tuteur = null;

    private bool $sendEmail = true;

    public function __construct(
        private readonly UserInterface $user
    ) {
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function getTuteur(): ?Tuteur
    {
        return $this->tuteur;
    }

    public function setTuteur(?Tuteur $tuteur): void
    {
        $this->tuteur = $tuteur;
    }

    public function isSendEmail(): bool
    {
        return $this->sendEmail;
    }

    public function setSendEmail(bool $sendEmail): void
    {
        $this->sendEmail = $sendEmail;
    }
}
