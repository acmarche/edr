<?php

namespace AcMarche\Edr\Controller\Admin;

use AcMarche\Edr\Animateur\Repository\AnimateurRepository;
use AcMarche\Edr\Entity\Security\User;
use AcMarche\Edr\User\Dto\AssociateUserAnimateurDto;
use AcMarche\Edr\User\Form\AssociateAnimateurType;
use AcMarche\Edr\User\Handler\AssociationAnimateurHandler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * User controller.
 */
#[Route(path: '/security/associer/animateur')]
#[IsGranted(data: 'ROLE_MERCREDI_ADMIN')]
final class AssocierAnimateurController extends AbstractController
{
    public function __construct(
        private readonly AssociationAnimateurHandler $associationAnimateurHandler,
        private readonly AnimateurRepository $animateurRepository
    ) {
    }

    #[Route(path: '/{id}', name: 'edr_user_associate_animateur', methods: ['GET', 'POST'])]
    public function associate(Request $request, User $user): Response
    {
        if (!$user->isAnimateur()) {
            $this->addFlash('danger', 'Le compte n\'a pas le rôle de animateur');

            return $this->redirectToRoute('edr_admin_user_show', [
                'id' => $user->getId(),
            ]);
        }

        $associateUserAnimateurDto = new AssociateUserAnimateurDto($user);
        $form = $this->createForm(AssociateAnimateurType::class, $associateUserAnimateurDto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->associationAnimateurHandler->handleAssociateAnimateur($associateUserAnimateurDto);

            return $this->redirectToRoute('edr_admin_user_show', [
                'id' => $user->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/user/associer_animateur.html.twig',
            [
                'user' => $user,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'edr_user_dissociate_animateur', methods: ['POST'])]
    public function dissociate(Request $request, User $user): RedirectResponse
    {
        if ($this->isCsrfTokenValid('dissociate' . $user->getId(), $request->request->get('_token'))) {
            $animateurId = (int) $request->request->get('animateur');
            if (0 === $animateurId) {
                $this->addFlash('danger', 'L\'animateur n\'a pas été trouvé');

                return $this->redirectToRoute('edr_admin_user_show', [
                    'id' => $user->getId(),
                ]);
            }

            $animateur = $this->animateurRepository->find($animateurId);
            $this->associationAnimateurHandler->handleDissociateAnimateur($user, $animateur);
        }

        return $this->redirectToRoute('edr_admin_user_show', [
            'id' => $user->getId(),
        ]);
    }
}
