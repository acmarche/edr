<?php

namespace AcMarche\Edr\Controller\Animateur;

use AcMarche\Edr\Animateur\Form\AnimateurType;
use AcMarche\Edr\Animateur\Message\AnimateurUpdated;
use AcMarche\Edr\Animateur\Repository\AnimateurRepository;
use AcMarche\Edr\Enfant\Repository\EnfantRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/animateur')]
#[IsGranted('ROLE_MERCREDI_ANIMATEUR')]
final class AnimateurController extends AbstractController
{
    use GetAnimateurTrait;

    public function __construct(
        private readonly AnimateurRepository $animateurRepository,
        private readonly EnfantRepository $enfantRepository,
        private readonly MessageBusInterface $dispatcher
    ) {
    }

    #[Route(path: '/', name: 'edr_animateur_animateur_show', methods: ['GET'])]
    public function show(): Response
    {
        if (($t = $this->hasAnimateur()) instanceof Response) {
            return $t;
        }

        $this->denyAccessUnlessGranted('animateur_show', $this->animateur);

        return $this->render(
            '@AcMarcheEdrAnimateur/animateur/show.html.twig',
            [
                'animateur' => $this->animateur,
            ]
        );
    }

    #[Route(path: '/edit', name: 'edr_animateur_animateur_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request): Response
    {
        if (($t = $this->hasAnimateur()) instanceof Response) {
            return $t;
        }

        $this->denyAccessUnlessGranted('animateur_edit', $this->animateur);
        $form = $this->createForm(AnimateurType::class, $this->animateur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->animateurRepository->flush();

            $this->dispatcher->dispatch(new AnimateurUpdated($this->animateur->getId()));

            return $this->redirectToRoute('edr_animateur_animateur_show');
        }

        return $this->render(
            '@AcMarcheEdrAnimateur/animateur/edit.html.twig',
            [
                'animateur' => $this->animateur,
                'form' => $form,
            ]
        );
    }
}
