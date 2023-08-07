<?php

namespace AcMarche\Edr\Controller\Admin;

use AcMarche\Edr\Entity\Security\User;
use AcMarche\Edr\Tuteur\Repository\TuteurRepository;
use AcMarche\Edr\User\Dto\AssociateUserTuteurDto;
use AcMarche\Edr\User\Form\AssociateTuteurType;
use AcMarche\Edr\User\Handler\AssociationTuteurHandler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * User controller.
 */
#[Route(path: '/security/associer/parent')]
#[IsGranted(data: 'ROLE_MERCREDI_ADMIN')]
final class AssocierParentController extends AbstractController
{
    public function __construct(
        private readonly AssociationTuteurHandler $associationHandler,
        private readonly TuteurRepository $tuteurRepository
    ) {
    }

    #[Route(path: '/{id}', name: 'edr_user_associate_tuteur', methods: ['GET', 'POST'])]
    public function associate(Request $request, User $user): Response
    {
        if (! $user->isParent()) {
            $this->addFlash('danger', 'Le compte n\'a pas le rôle de parent');

            return $this->redirectToRoute('edr_admin_user_show', [
                'id' => $user->getId(),
            ]);
        }

        $associateUserTuteurDto = new AssociateUserTuteurDto($user);
        $this->associationHandler->suggestTuteur($user, $associateUserTuteurDto);
        $form = $this->createForm(AssociateTuteurType::class, $associateUserTuteurDto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->associationHandler->handleAssociateTuteur($associateUserTuteurDto);

            return $this->redirectToRoute('edr_admin_user_show', [
                'id' => $user->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/user/associer_tuteur.html.twig',
            [
                'user' => $user,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'edr_user_dissociate_tuteur', methods: ['POST'])]
    public function dissociate(Request $request, User $user): RedirectResponse
    {
        if ($this->isCsrfTokenValid('dissociate'.$user->getId(), $request->request->get('_token'))) {
            $tuteurId = (int) $request->request->get('tuteur');
            if (0 === $tuteurId) {
                $this->addFlash('danger', 'Le parent n\'a pas été trouvé');

                return $this->redirectToRoute('edr_admin_user_show', [
                    'id' => $user->getId(),
                ]);
            }

            $tuteur = $this->tuteurRepository->find($tuteurId);
            $this->associationHandler->handleDissociateTuteur($user, $tuteur);
        }

        return $this->redirectToRoute('edr_admin_user_show', [
            'id' => $user->getId(),
        ]);
    }
}
