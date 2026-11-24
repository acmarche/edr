<?php

namespace AcMarche\Edr\User\Handler;

use AcMarche\Edr\Entity\Security\User;
use AcMarche\Edr\Entity\Tuteur;
use AcMarche\Edr\Mailer\Factory\UserEmailFactory;
use AcMarche\Edr\Mailer\NotificationMailer;
use AcMarche\Edr\Tuteur\Repository\TuteurRepository;
use AcMarche\Edr\User\Dto\AssociateUserTuteurDto;
use AcMarche\Edr\User\Factory\UserFactory;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

final class AssociationTuteurHandler
{
    private readonly FlashBagInterface $flashBag;

    public function __construct(
        private readonly TuteurRepository $tuteurRepository,
        private readonly UserFactory $userFactory,
        private readonly NotificationMailer $notificationMailer,
        private readonly UserEmailFactory $userEmailFactory,
        RequestStack $requestStack
    ) {
        $this->flashBag = $requestStack->getSession()->getFlashBag();
    }

    public function suggestTuteur(User $user, AssociateUserTuteurDto $associateUserTuteurDto): void
    {
        try {
            $tuteur = $this->tuteurRepository->findOneByEmail($user->getEmail());
            if ($tuteur instanceof Tuteur) {
                $associateUserTuteurDto->setTuteur($tuteur);
            }
        } catch (NonUniqueResultException) {
        }
    }

    public function handleAssociateTuteur(AssociateUserTuteurDto $associateUserTuteurDto): void
    {
        $tuteur = $associateUserTuteurDto->getTuteur();
        $user = $associateUserTuteurDto->getUser();

        if ([] !== $this->tuteurRepository->getTuteursByUser($user)) {
            //remove old tuteur
            $user->getTuteurs()->clear();
        }

        if (!$tuteur instanceof Tuteur) {
            $this->flashBag->add('danger', 'Aucun tuteur sélectionné.');

            return;
        }

        $user->addTuteur($tuteur);
        $this->tuteurRepository->flush();

        $this->flashBag->add('success', 'L\'utilisateur a bien été associé.');

        if ($associateUserTuteurDto->isSendEmail()) {
            $message = $this->userEmailFactory->messageNewAccountToTuteur($user, $tuteur);
            $this->notificationMailer->sendAsEmailNotification($message, $user->getEmail());
            $this->flashBag->add('success', 'Un mail de bienvenue a été envoyé');
        }
    }

    public function handleDissociateTuteur(User $user, Tuteur $tuteur): Tuteur
    {
        $user->removeTuteur($tuteur);

        $this->tuteurRepository->flush();
        $this->flashBag->add('success', 'Le parent a bien été dissocié.');

        return $tuteur;
    }

    public function handleCreateUserFromTuteur(Tuteur $tuteur): ?User
    {
        $user = $this->userFactory->newFromTuteur($tuteur);
        $plainPassword = $user->getPlainPassword();

        $message = $this->userEmailFactory->messageNewAccountToTuteur($user, $tuteur, $plainPassword);
        $this->notificationMailer->sendAsEmailNotification($message, $user->getEmail());
        $this->flashBag->add('success', 'Un mail de bienvenue a été envoyé');

        return $user;
    }
}
