<?php

namespace AcMarche\Edr\User\Factory;

use AcMarche\Edr\Entity\Animateur;
use AcMarche\Edr\Entity\Security\User;
use AcMarche\Edr\Entity\Tuteur;
use AcMarche\Edr\Security\PasswordGenerator;
use AcMarche\Edr\Security\Role\EdrSecurityRole;
use AcMarche\Edr\User\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserFactory
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $userPasswordHasher
    ) {
    }

    public function getInstance(?string $email = null): ?User
    {
        $user = new User();
        if ($email && !$user = $this->userRepository->findOneByEmailOrUserName($email)) {
            $user = new User();
            $user->setEmail($email);
            $user->setUsername($email);
        }

        $user->setEnabled(true);

        return $user;
    }

    public function newFromAnimateur(Animateur $animateur, ?User $user = null): ?User
    {
        if (!$user instanceof User) {
            $user = $this->getInstance($animateur->getEmail());
            $user->setNom($animateur->getNom());
            $user->setPrenom($animateur->getPreNom());
            if ($animateur->getEmail()) {
                $user->setEmail($animateur->getEmail());
            }
        }

        $user->setUsername($user->getEmail());
        $user->setUsername($user->getEmail());
        $user->setPlainPassword(PasswordGenerator::generatePassword());
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $user->getPlainPassword()));
        $user->addRole(EdrSecurityRole::ROLE_ANIMATEUR);

        $this->userRepository->persist($user);
        $this->userRepository->flush();

        return $user;
    }

    public function newFromTuteur(Tuteur $tuteur, ?User $user = null): ?User
    {
        if (!$user instanceof User) {
            $user = $this->getInstance($tuteur->getEmail());
            $user->setNom($tuteur->getNom());
            $user->setPrenom($tuteur->getPreNom());
            if ($tuteur->getEmail()) {
                $user->setEmail($tuteur->getEmail());
            }
        }

        $user->setUsername($user->getEmail());
        $user->setPlainPassword(PasswordGenerator::generatePassword());
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $user->getPlainPassword()));

        $user->addTuteur($tuteur);
        $user->addRole(EdrSecurityRole::ROLE_PARENT);

        $this->userRepository->persist($user);
        $this->userRepository->flush();

        return $user;
    }
}
