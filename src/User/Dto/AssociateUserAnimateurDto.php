<?php

namespace AcMarche\Edr\User\Dto;

use AcMarche\Edr\Entity\Animateur;
use Symfony\Component\Security\Core\User\UserInterface;

final class AssociateUserAnimateurDto
{
    private ?Animateur $animateur = null;

    private bool $sendEmail = true;

    public function __construct(
        private UserInterface $user
    ) {
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function getAnimateur(): ?Animateur
    {
        return $this->animateur;
    }

    public function setAnimateur(Animateur $tuteur): void
    {
        $this->animateur = $tuteur;
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
