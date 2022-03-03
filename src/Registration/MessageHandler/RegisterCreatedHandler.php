<?php

namespace AcMarche\Edr\Registration\MessageHandler;

use AcMarche\Edr\Mailer\Factory\RegistrationMailerFactory;
use AcMarche\Edr\Mailer\NotificationMailer;
use AcMarche\Edr\Parameter\Option;
use AcMarche\Edr\Registration\Message\RegisterCreated;
use AcMarche\Edr\User\Repository\UserRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

final class RegisterCreatedHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(
        private UserRepository $userRepository,
        RequestStack $requestStack,
        private RegistrationMailerFactory $registrationMailerFactory,
        private VerifyEmailHelperInterface $verifyEmailHelper,
        private NotificationMailer $notificationMailer,
        private ParameterBagInterface $parameterBag
    ) {
        $this->flashBag = $requestStack->getSession()?->getFlashBag();
    }

    public function __invoke(RegisterCreated $registerCreated): void
    {
        $userId = $registerCreated->getUserId();
        $user = $this->userRepository->find($userId);

        // generate a signed url and email it to the user
        /* $this->emailVerifier->sendEmailConfirmation(
             'app_verify_email',
             $user,
             $this->registrationMailerFactory->generateMessagToVerifyEmail($user)
         );*/

        $verifyEmailSignatureComponents = $this->verifyEmailHelper->generateSignature(
            'app_verify_email',
            $user->getId(),
            $user->getEmail()
        );

        $message = $this->registrationMailerFactory->generateMessagRegisgerSuccess(
            $user,
            $verifyEmailSignatureComponents
        );
        $this->notificationMailer->sendAsEmailNotification($message, $user->getEmail());

        $message = $this->registrationMailerFactory->generateMessageToAdminAccountCreated($user);
        $this->notificationMailer->sendAsEmailNotification($message, $user->getEmail());

        $this->flashBag->add('success', 'Votre compte a bien été créé, consultez votre boite mail');
    }

    public function isOpen(): bool
    {
        $register = (bool) $this->parameterBag->get(Option::REGISTER);

        return true === $register;
    }

    /**
     * @throws VerifyEmailExceptionInterface
     */
    public function handleEmailConfirmation(Request $request, UserInterface $user): void
    {
        $this->verifyEmailHelper->validateEmailConfirmation($request->getUri(), $user->getId(), $user->getEmail());

        $user->setIsVerified(true);

        $this->userRepository->persist($user);
        $this->userRepository->flush();
    }
}
