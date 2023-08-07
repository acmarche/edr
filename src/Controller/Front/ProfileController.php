<?php

namespace AcMarche\Edr\Controller\Front;

use AcMarche\Edr\Entity\Security\User;
use AcMarche\Edr\User\Form\UserEditType;
use AcMarche\Edr\User\Form\UserPasswordType;
use AcMarche\Edr\User\Message\UserUpdated;
use AcMarche\Edr\User\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/profile')]
#[IsGranted(data: 'IS_AUTHENTICATED_FULLY')]
final class ProfileController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $userPasswordEncoder,
        private readonly MessageBusInterface $dispatcher
    ) {
    }

    #[Route(path: '/show', name: 'edr_front_user_show')]
    public function show(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user instanceof User) {
            $this->addFlash('warning', "Votre compte n'est pas encore actif");

            return $this->redirectToRoute('edr_front_home');
        }

        return $this->render(
            '@AcMarcheEdr/front/user/show.html.twig',
            [
                'user' => $user,
            ]
        );
    }

    #[Route(path: '/redirect', name: 'edr_front_profile_redirect')]
    public function redirectByProfile(): RedirectResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($user instanceof User) {
            $roles = $user->getRoles();
            $del_val = 'ROLE_USER';

            $roles = array_filter(
                $roles,
                static fn ($e) => $e !== $del_val
            );

            if (\count($roles) > 1) {
                return $this->redirectToRoute('edr_front_select_profile');
            }

            if ($user->hasRole('ROLE_MERCREDI_PARENT')) {
                return $this->redirectToRoute('edr_parent_home');
            }

            if ($user->hasRole('ROLE_MERCREDI_ECOLE')) {
                return $this->redirectToRoute('edr_ecole_home');
            }

            if ($user->hasRole('ROLE_MERCREDI_ANIMATEUR')) {
                return $this->redirectToRoute('edr_animateur_home');
            }

            if ($user->hasRole('ROLE_MERCREDI_ADMIN') || $user->hasRole('ROLE_MERCREDI_READ')) {
                return $this->redirectToRoute('edr_admin_home');
            }
        }

        $this->addFlash('warning', 'Aucun rôle ne vous a été attribué');

        return $this->redirectToRoute('edr_front_home');
    }

    #[Route(path: '/select', name: 'edr_front_select_profile')]
    #[IsGranted(data: 'ROLE_MERCREDI')]
    public function selectProfile(): Response
    {
        return $this->render(
            '@AcMarcheEdr/front/user/select_profile.html.twig',
            [
            ]
        );
    }

    #[Route(path: '/edit', name: 'edr_front_user_edit')]
    #[IsGranted(data: 'ROLE_MERCREDI')]
    public function edit(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->userRepository->flush();

            $this->dispatcher->dispatch(new UserUpdated($user->getId()));

            return $this->redirectToRoute('edr_front_user_show');
        }

        return $this->render(
            '@AcMarcheEdr/front/user/edit.html.twig',
            [
                'user' => $user,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/password', name: 'edr_front_user_password')]
    #[IsGranted(data: 'ROLE_MERCREDI')]
    public function password(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserPasswordType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->userPasswordEncoder->hashPassword($user, $form->getData()->getPlainPassword());
            $user->setPassword($password);
            $this->userRepository->flush();
            $this->addFlash('success', 'Le mot de passe a bien été modifié');

            return $this->redirectToRoute('edr_front_user_show');
        }

        return $this->render(
            '@AcMarcheEdr/front/user/password.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}
