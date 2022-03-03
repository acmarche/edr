<?php

namespace AcMarche\Edr\Controller\Parent;

use AcMarche\Edr\Tuteur\Form\TuteurType;
use AcMarche\Edr\Tuteur\Message\TuteurUpdated;
use AcMarche\Edr\Tuteur\Repository\TuteurRepository;
use AcMarche\Edr\Tuteur\Utils\TuteurUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;


#[Route(path: '/tuteur')]
final class TuteurController extends AbstractController
{
    use GetTuteurTrait;

    public function __construct(
        private TuteurRepository $tuteurRepository,
        private MessageBusInterface $dispatcher
    ) {
    }

    #[Route(path: '/', name: 'edr_parent_tuteur_show', methods: ['GET'])]
    #[IsGranted(data: 'ROLE_MERCREDI_PARENT')]
    public function show(): Response
    {
        if (($hasTuteur = $this->hasTuteur()) !== null) {
            return $hasTuteur;
        }
        $tuteurIsComplete = TuteurUtils::coordonneesIsComplete($this->tuteur);

        return $this->render(
            '@AcMarcheEdrParent/tuteur/show.html.twig',
            [
                'tuteurIsComplete' => $tuteurIsComplete,
                'tuteur' => $this->tuteur,
            ]
        );
    }

    #[Route(path: '/edit', name: 'edr_parent_tuteur_edit', methods: ['GET', 'POST'])]
    #[IsGranted(data: 'ROLE_MERCREDI_PARENT')]
    public function edit(Request $request): Response
    {
        if (($hasTuteur = $this->hasTuteur()) !== null) {
            return $hasTuteur;
        }
        $form = $this->createForm(TuteurType::class, $this->tuteur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->tuteurRepository->flush();

            $this->dispatcher->dispatch(new TuteurUpdated($this->tuteur->getId()));

            return $this->redirectToRoute('edr_parent_tuteur_show');
        }

        return $this->render(
            '@AcMarcheEdrParent/tuteur/edit.html.twig',
            [
                'tuteur' => $this->tuteur,
                'form' => $form->createView(),
            ]
        );
    }
}
