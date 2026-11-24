<?php

namespace AcMarche\Edr\Registration\MessageHandler;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use AcMarche\Edr\Mailer\Factory\RegistrationMailerFactory;
use AcMarche\Edr\Mailer\NotificationMailer;
use AcMarche\Edr\Parameter\Option;
use AcMarche\Edr\Registration\Message\RegisterCreated;
use AcMarche\Edr\User\Repository\UserRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

use Symfony\Component\Security\Core\User\UserInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

#[AsMessageHandler]
final class RegisterCreatedHandler
{
    private readonly FlashBagInterface $flashBag;
    public function __construct(
        private readonly UserRepository $userRepository,
        RequestStack $requestStack,
        private readonly RegistrationMailerFactory $registrationMailerFactory,
        private readonly VerifyEmailHelperInterface $verifyEmailHelper,
        private readonly NotificationMailer $notificationMailer,
        private readonly ParameterBagInterface $parameterBag
    ) {
        $this->flashBag = $requestStack->getSession()->getFlashBag();
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
        return (bool) $this->parameterBag->get(Option::REGISTER);
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
