<?php

namespace AcMarche\Edr\Controller\Front;

use AcMarche\Edr\Entity\Security\User;
use AcMarche\Edr\Mailer\Factory\RegistrationMailerFactory;
use AcMarche\Edr\Mailer\NotificationMailer;
use AcMarche\Edr\ResetPassword\Form\ChangePasswordFormType;
use AcMarche\Edr\ResetPassword\Form\ResetPasswordRequestFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

#[Route(path: '/reset-password')]
class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    public function __construct(
        private readonly ResetPasswordHelperInterface $resetPasswordHelper,
        private readonly RegistrationMailerFactory $registrationMailerFactory,
        private readonly NotificationMailer $notificationMailer,
        private readonly ManagerRegistry $managerRegistry
    ) {
    }

    #[Route(path: '/', name: 'edr_front_forgot_password_request')]
    public function request(Request $request): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->processSendingPasswordResetEmail(
                $form->get('email')->getData()
            );
        }

        return $this->render(
            '@AcMarcheEdr/front/reset_password/request.html.twig',
            [
                'requestForm' => $form,
            ]
        );
    }

    /**
     * Confirmation page after a user has requested a password reset.
     */
    #[Route(path: '/check-email', name: 'edr_front_check_email')]
    public function checkEmail(): Response
    {
        // Generate a fake token if the user does not exist or someone hit this page directly.
        // This prevents exposing whether or not a user was found with the given email address or not
        if (!($resetToken = $this->getTokenObjectFromSession()) instanceof ResetPasswordToken) {
            $resetToken = $this->resetPasswordHelper->generateFakeResetToken();
        }

        return $this->render(
            '@AcMarcheEdr/front/reset_password/check_email.html.twig',
            [
                'resetToken' => $resetToken,
            ]
        );
    }

    /**
     * Validates and process the reset URL that the user clicked in their email.
     */
    #[Route(path: '/reset/{token}', name: 'edr_front_reset_password')]
    public function reset(Request $request, UserPasswordHasherInterface $passwordEncoder, string $token = null): Response
    {
        if ($token) {
            // We store the token in session and remove it from the URL, to avoid the URL being
            // loaded in a browser and potentially leaking the token to 3rd party JavaScript.
            $this->storeTokenInSession($token);

            return $this->redirectToRoute('edr_front_reset_password');
        }

        $token = $this->getTokenFromSession();
        if (null === $token) {
            throw $this->createNotFoundException('No reset password token found in the URL or in the session.');
        }

        try {
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $resetPasswordException) {
            $this->addFlash(
                'danger',
                sprintf(
                    'There was a problem validating your reset request - %s',
                    $resetPasswordException->getReason()
                )
            );

            return $this->redirectToRoute('edr_front_forgot_password_request');
        }

        // The token is valid; allow the user to change their password.
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // A password reset token should be used only once, remove it.
            $this->resetPasswordHelper->removeResetRequest($token);

            // Encode the plain password, and set it.
            $encodedPassword = $passwordEncoder->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            );

            $user->setPassword($encodedPassword);
            $this->managerRegistry->getManager()->flush();

            // The session is cleaned up after the password has been changed.
            $this->cleanSessionAfterReset();

            return $this->redirectToRoute('edr_front_home');
        }

        return $this->render(
            '@AcMarcheEdr/front/reset_password/reset.html.twig',
            [
                'resetForm' => $form,
            ]
        );
    }

    private function processSendingPasswordResetEmail(string $emailFormData): RedirectResponse
    {
        $user = $this->managerRegistry->getRepository(User::class)->findOneBy(
            [
                'email' => $emailFormData,
            ]
        );

        // Do not reveal whether a user account was found or not.
        if (!$user instanceof User) {
            //$this->addFlash('danger', 'Utilisateur non trouvé');

            return $this->redirectToRoute('edr_front_forgot_password_request');
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $resetPasswordException) {
            // If you want to tell the user why a reset email was not sent, uncomment
            // the lines below and change the redirect to 'app_forgot_password_request'.
            // Caution: This may reveal if a user is registered or not.
            //
            $this->addFlash(
                'danger',
                sprintf(
                    'There was a problem handling your password reset request - %s',
                    $resetPasswordException->getReason()
                )
            );

            return $this->redirectToRoute('edr_front_forgot_password_request');
        }

        $message = $this->registrationMailerFactory->messageSendLinkLostPassword($user, $resetToken);
        $this->notificationMailer->sendAsEmailNotification($message, $user->getEmail());

        // Store the token object in session for retrieval in check-email route.
        $this->setTokenObjectInSession($resetToken);

        return $this->redirectToRoute('edr_front_check_email');
    }
}
