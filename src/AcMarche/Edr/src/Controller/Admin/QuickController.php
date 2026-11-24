<?php

namespace AcMarche\Edr\Controller\Admin;

use AcMarche\Edr\Enfant\Repository\EnfantRepository;
use AcMarche\Edr\Entity\Relation;
use AcMarche\Edr\Relation\Dto\TuteurEnfantDto;
use AcMarche\Edr\Relation\Form\TuteurEnfantQuickType;
use AcMarche\Edr\Relation\Repository\RelationRepository;
use AcMarche\Edr\Tuteur\Repository\TuteurRepository;
use AcMarche\Edr\User\Handler\AssociationTuteurHandler;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[IsGranted('ROLE_MERCREDI_ADMIN')]
#[Route(path: '/parent_enfant')]
final class QuickController extends AbstractController
{
    public function __construct(
        private readonly TuteurRepository $tuteurRepository,
        private readonly EnfantRepository $enfantRepository,
        private readonly RelationRepository $relationRepository,
        private readonly AssociationTuteurHandler $associationHandler
    ) {
    }

    #[Route(path: '/', name: 'edr_admin_parent_enfant_new')]
    public function new(Request $request): Response
    {
        $form = $this->createForm(TuteurEnfantQuickType::class, new TuteurEnfantDto());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $tuteur = $form->getData()->getTuteur();
            $enfant = $form->getData()->getEnfant();

            $this->tuteurRepository->persist($tuteur);
            $this->tuteurRepository->flush();
            $this->enfantRepository->persist($enfant);
            $this->enfantRepository->flush();

            $relation = new Relation($tuteur, $enfant);

            $this->relationRepository->persist($relation);
            $this->relationRepository->flush();
            $user = null;
            $password = null;

            if ($tuteur->getEmail()) {
                $user = $this->associationHandler->handleCreateUserFromTuteur($tuteur);
                $password = $user->getPlainPassword();
                $this->addFlash('success', 'Un compte a été créé pour le parent');
            }

            return $this->render(
                '@AcMarcheEdrAdmin/quick/created.html.twig',
                [
                    'enfant' => $enfant,
                    'tuteur' => $tuteur,
                    'user' => $user,
                    'password' => $password,
                ]
            );
        }

        return $this->render(
            '@AcMarcheEdrAdmin/quick/new.html.twig',
            [
                'form' => $form,
            ]
        );
    }
}
