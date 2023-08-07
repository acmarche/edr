<?php

namespace AcMarche\Edr\Controller\Admin;

use AcMarche\Edr\Entity\Security\User;
use AcMarche\Edr\Entity\Tuteur;
use AcMarche\Edr\Security\Role\EdrSecurityRole;
use AcMarche\Edr\User\Form\UserEditType;
use AcMarche\Edr\User\Form\UserRoleType;
use AcMarche\Edr\User\Form\UserSearchType;
use AcMarche\Edr\User\Form\UserType;
use AcMarche\Edr\User\Message\UserCreated;
use AcMarche\Edr\User\Message\UserDeleted;
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

#[Route(path: '/user')]
#[IsGranted(data: 'ROLE_MERCREDI_ADMIN')]
final class UserController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $userPasswordEncoder,
        private readonly MessageBusInterface $dispatcher
    ) {
    }

    /**
     * Lists all User entities.
     */
    #[Route(path: '/', name: 'edr_admin_user_index', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $users = [];
        $form = $this->createForm(UserSearchType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $nom = $data['nom'];
            $role = $data['role'];

            $users = $this->userRepository->findByNameOrRoles($nom, $role);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/user/index.html.twig',
            [
                'users' => $users,
                'form' => $form->createView(),
                'search' => $form->isSubmitted(),
            ]
        );
    }

    /**
     * Displays a form to create a new User utilisateur.
     */
    #[Route(path: '/new', name: 'edr_admin_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $this->userPasswordEncoder->hashPassword($user, $form->get('plainPassword')->getData())
            );
            $user->setUsername($user->getEmail());
            $this->userRepository->persist($user);
            $this->userRepository->flush();
            $this->dispatcher->dispatch(new UserCreated($user->getId()));

            return $this->redirectToRoute('edr_admin_user_show', [
                'id' => $user->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/user/new.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Displays a form to create a new User utilisateur.
     */
    #[Route(path: '/new/tuteur/{id}', name: 'edr_admin_user_new_from_tuteur', methods: ['GET', 'POST'])]
    public function newFromTuteur(Request $request, Tuteur $tuteur): Response
    {
        $user = User::newFromObject($tuteur);
        $user->addRole(EdrSecurityRole::ROLE_PARENT);
        $user->addTuteur($tuteur);

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $this->userPasswordEncoder->hashPassword($user, $form->get('plainPassword')->getData())
            );
            $user->setUsername($user->getEmail());
            $this->userRepository->persist($user);
            $this->userRepository->flush();
            $this->dispatcher->dispatch(new UserCreated($user->getId()));

            return $this->redirectToRoute('edr_admin_user_show', [
                'id' => $user->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/user/new.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Finds and displays a User utilisateur.
     */
    #[Route(path: '/{id}', name: 'edr_admin_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render(
            '@AcMarcheEdrAdmin/user/show.html.twig',
            [
                'user' => $user,
            ]
        );
    }

    /**
     * Displays a form to edit an existing User utilisateur.
     */
    #[Route(path: '/{id}/edit', name: 'edr_admin_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user): Response
    {
        $editForm = $this->createForm(UserEditType::class, $user);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->userRepository->flush();
            $this->dispatcher->dispatch(new UserUpdated($user->getId()));

            return $this->redirectToRoute('edr_admin_user_show', [
                'id' => $user->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/user/edit.html.twig',
            [
                'user' => $user,
                'form' => $editForm->createView(),
            ]
        );
    }

    /**
     * Displays a form to edit an existing User utilisateur.
     */
    #[Route(path: '/{id}/roles', name: 'edr_admin_user_roles', methods: ['GET', 'POST'])]
    public function roles(Request $request, User $user): Response
    {
        $form = $this->createForm(UserRoleType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->userRepository->flush();
            $this->dispatcher->dispatch(new UserUpdated($user->getId()));

            return $this->redirectToRoute('edr_admin_user_show', [
                'id' => $user->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/user/roles_edit.html.twig',
            [
                'user' => $user,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Deletes a User utilisateur.
     */
    #[Route(path: '/{id}/delete', name: 'edr_admin_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $id = $user->getId();
            $this->userRepository->remove($user);
            $this->userRepository->flush();
            $this->dispatcher->dispatch(new UserDeleted($id));
        }

        return $this->redirectToRoute('edr_admin_user_index');
    }
}
