<?php

namespace AcMarche\Edr\Controller\Admin;

use AcMarche\Edr\Entity\Security\User;
use AcMarche\Edr\User\Form\UserPasswordType;
use AcMarche\Edr\User\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/utilisateur/password')]
#[IsGranted(data: 'ROLE_MERCREDI_ADMIN')]
final class PasswordController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $userPasswordEncoder
    ) {
    }

    /**
     * Displays a form to edit an existing Utilisateur utilisateur.
     */
    #[Route(path: '/{id}/password', name: 'edr_admin_user_password', methods: ['GET', 'POST'])]
    public function passord(Request $request, User $user): Response
    {
        $form = $this->createForm(UserPasswordType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->userPasswordEncoder->hashPassword($user, $form->getData()->getPlainPassword());
            $user->setPassword($password);
            $this->userRepository->flush();
            $this->addFlash('success', 'Le mot de passe a bien été modifié');

            return $this->redirectToRoute('edr_admin_user_show', [
                'id' => $user->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/user/password.html.twig',
            [
                'user' => $user,
                'form' => $form->createView(),
            ]
        );
    }
}
