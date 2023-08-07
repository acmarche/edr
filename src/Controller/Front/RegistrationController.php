<?php

namespace AcMarche\Edr\Controller\Front;

use AcMarche\Edr\Entity\Security\User;
use AcMarche\Edr\Registration\Form\RegistrationFormType;
use AcMarche\Edr\Registration\Message\RegisterCreated;
use AcMarche\Edr\Registration\MessageHandler\RegisterCreatedHandler;
use AcMarche\Edr\User\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

final class RegistrationController extends AbstractController
{
    public function __construct(
        private readonly RegisterCreatedHandler $registerCreatedHandler,
        private readonly UserPasswordHasherInterface $userPasswordEncoder,
        private readonly UserRepository $userRepository,
        private readonly MessageBusInterface $dispatcher
    ) {
    }

    #[Route(path: '/register', name: 'edr_front_register')]
    public function register(Request $request): Response
    {
        if (! $this->registerCreatedHandler->isOpen()) {
            return $this->redirectToRoute('edr_front_home');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $this->userPasswordEncoder->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setUsername($user->getEmail());
            $user->setEnabled(true);

            $this->userRepository->persist($user);
            $this->userRepository->flush();

            $this->dispatcher->dispatch(new RegisterCreated($user->getId()));

            return $this->redirectToRoute('edr_front_home');
        }

        return $this->render(
            '@AcMarcheEdr/front/registration/register.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request): RedirectResponse
    {
        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->registerCreatedHandler->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $verifyEmailException) {
            $this->addFlash('verify_email_error', $verifyEmailException->getReason());

            return $this->redirectToRoute('edr_front_register');
        }

        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('edr_front_register');
    }
}
