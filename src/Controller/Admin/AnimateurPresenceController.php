<?php

namespace AcMarche\Edr\Controller\Admin;

use AcMarche\Edr\Animateur\Form\AnimateurJourType;
use AcMarche\Edr\Animateur\Repository\AnimateurRepository;
use AcMarche\Edr\Entity\Animateur;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/animateur/presences')]
#[IsGranted(data: 'ROLE_MERCREDI_ADMIN')]
final class AnimateurPresenceController extends AbstractController
{
    public function __construct(
        private readonly AnimateurRepository $animateurRepository
    ) {
    }

    #[Route(path: '/{id}/edit', name: 'edr_admin_animateur_presence_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Animateur $animateur): Response
    {
        $form = $this->createForm(AnimateurJourType::class, $animateur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->animateurRepository->flush();

            return $this->redirectToRoute('edr_admin_animateur_show', [
                'id' => $animateur->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/animateur/presences_edit.html.twig',
            [
                'animateur' => $animateur,
                'form' => $form->createView(),
            ]
        );
    }
}
