<?php

namespace AcMarche\Edr\User\Handler;

use AcMarche\Edr\Animateur\Repository\AnimateurRepository;
use AcMarche\Edr\Entity\Animateur;
use AcMarche\Edr\Entity\Security\User;
use AcMarche\Edr\Mailer\Factory\UserEmailFactory;
use AcMarche\Edr\Mailer\NotificationMailer;
use AcMarche\Edr\User\Dto\AssociateUserAnimateurDto;
use AcMarche\Edr\User\Factory\UserFactory;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

final class AssociationAnimateurHandler
{
    private readonly FlashBagInterface $flashBag;

    public function __construct(
        private readonly AnimateurRepository $animateurRepository,
        private readonly UserFactory $userFactory,
        private readonly NotificationMailer $notificationMailer,
        private readonly UserEmailFactory $userEmailFactory,
        RequestStack $requestStack
    ) {
        $this->flashBag = $requestStack->getSession()->getFlashBag();
    }

    public function suggestAnimateur(User $user, AssociateUserAnimateurDto $associateUserAnimateurDto): void
    {
        $animateur = $this->animateurRepository->findOneByEmail($user->getEmail());
        if ($animateur instanceof Animateur) {
            $associateUserAnimateurDto->setAnimateur($animateur);
        }
    }

    public function handleAssociateAnimateur(AssociateUserAnimateurDto $associateUserAnimateurDto): void
    {
        $animateur = $associateUserAnimateurDto->getAnimateur();
        $user = $associateUserAnimateurDto->getUser();

        if ((is_countable($this->animateurRepository->getAnimateursByUser($user)) ? \count($this->animateurRepository->getAnimateursByUser($user)) : 0) > 0) {
            //remove old animateur
            $user->getAnimateurs()->clear();
        }

        $user->addAnimateur($animateur);
        $this->animateurRepository->flush();

        $this->flashBag->add('success', 'L\'utilisateur a bien été associé.');

        if ($associateUserAnimateurDto->isSendEmail()) {
            $message = $this->userEmailFactory->messageNewAccountToAnimateur($user, $animateur);
            $this->notificationMailer->sendAsEmailNotification($message, $user->getEmail());
            $this->flashBag->add('success', 'Un mail de bienvenue a été envoyé');
        }
    }

    public function handleDissociateAnimateur(User $user, Animateur $animateur): Animateur
    {
        $user->removeAnimateur($animateur);

        $this->animateurRepository->flush();
        $this->flashBag->add('success', 'L\'animateur a bien été dissocié.');

        return $animateur;
    }

    public function handleCreateUserFromAnimateur(Animateur $animateur): ?User
    {
        $user = $this->userFactory->newFromAnimateur($animateur);
        $plainPassword = $user->getPlainPassword();

        $message = $this->userEmailFactory->messageNewAccountToAnimateur($user, $animateur, $plainPassword);
        $this->notificationMailer->sendAsEmailNotification($message, $user->getEmail());

        return $user;
    }
}
