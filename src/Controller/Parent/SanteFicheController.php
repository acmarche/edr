<?php

namespace AcMarche\Edr\Controller\Parent;

use AcMarche\Edr\Enfant\Message\EnfantUpdated;
use AcMarche\Edr\Entity\Enfant;
use AcMarche\Edr\Organisation\Repository\OrganisationRepository;
use AcMarche\Edr\Sante\Form\Etape\SanteFicheEtape1Type;
use AcMarche\Edr\Sante\Form\Etape\SanteFicheEtape2Type;
use AcMarche\Edr\Sante\Form\Etape\SanteFicheEtape3Type;
use AcMarche\Edr\Sante\Handler\SanteHandler;
use AcMarche\Edr\Sante\Message\SanteFicheUpdated;
use AcMarche\Edr\Sante\Repository\SanteFicheRepository;
use AcMarche\Edr\Sante\Repository\SanteQuestionRepository;
use AcMarche\Edr\Sante\Utils\SanteChecker;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/santeFiche')]
#[IsGranted(data: 'ROLE_MERCREDI_PARENT')]
final class SanteFicheController extends AbstractController
{
    public function __construct(
        private readonly SanteQuestionRepository $santeQuestionRepository,
        private readonly OrganisationRepository $organisationRepository,
        private readonly SanteHandler $santeHandler,
        private readonly SanteChecker $santeChecker,
        private readonly SanteFicheRepository $santeFicheRepository,
        private readonly MessageBusInterface $dispatcher
    ) {
    }

    #[Route(path: '/{uuid}', name: 'edr_parent_sante_fiche_show', methods: ['GET'])]
    #[IsGranted(data: 'enfant_show', subject: 'enfant')]
    public function show(Enfant $enfant): Response
    {
        $santeFiche = $this->santeHandler->init($enfant);
        if (! $santeFiche->getId()) {
            $this->addFlash('warning', 'Cette enfant n\'a pas encore de fiche santé');

            return $this->redirectToRoute('edr_parent_sante_fiche_edit', [
                'uuid' => $enfant->getUuid(),
            ]);
        }

        $isComplete = $this->santeChecker->isComplete($santeFiche);
        $questions = $this->santeQuestionRepository->findAllOrberByPosition();
        $organisation = $this->organisationRepository->getOrganisation();

        return $this->render(
            '@AcMarcheEdrParent/sante_fiche/show.html.twig',
            [
                'enfant' => $enfant,
                'sante_fiche' => $santeFiche,
                'is_complete' => $isComplete,
                'questions' => $questions,
                'organisation' => $organisation,
            ]
        );
    }

    #[Route(path: '/{uuid}/edit/etape1', name: 'edr_parent_sante_fiche_edit', methods: ['GET', 'POST'])]
    #[IsGranted(data: 'enfant_edit', subject: 'enfant')]
    public function edit(Request $request, Enfant $enfant): Response
    {
        $form = $this->createForm(SanteFicheEtape1Type::class, $enfant);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->santeFicheRepository->flush();
            $this->dispatcher->dispatch(new EnfantUpdated($enfant->getId()));

            return $this->redirectToRoute('edr_parent_sante_fiche_edit_etape2', [
                'uuid' => $enfant->getUuid(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdr/parent/sante_fiche/edit/etape1.html.twig',
            [
                'enfant' => $enfant,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{uuid}/edit/etape2', name: 'edr_parent_sante_fiche_edit_etape2', methods: ['GET', 'POST'])]
    #[IsGranted(data: 'enfant_edit', subject: 'enfant')]
    public function editEtape2(Request $request, Enfant $enfant): Response
    {
        $santeFiche = $this->santeHandler->init($enfant, false);
        if ([] === $santeFiche->getAccompagnateurs()) {
            $santeFiche->addAccompagnateur(' ');
        }

        $form = $this->createForm(SanteFicheEtape2Type::class, $santeFiche);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->santeFicheRepository->flush();
            $this->dispatcher->dispatch(new SanteFicheUpdated($santeFiche->getId()));

            return $this->redirectToRoute('edr_parent_sante_fiche_edit_etape3', [
                'uuid' => $enfant->getUuid(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdr/parent/sante_fiche/edit/etape2.html.twig',
            [
                'sante_fiche' => $santeFiche,
                'enfant' => $enfant,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{uuid}/edit/etape3', name: 'edr_parent_sante_fiche_edit_etape3', methods: ['GET', 'POST'])]
    #[IsGranted(data: 'enfant_edit', subject: 'enfant')]
    public function editEtape3(Request $request, Enfant $enfant): Response
    {
        $santeFiche = $this->santeHandler->init($enfant);
        $form = $this->createForm(SanteFicheEtape3Type::class, $santeFiche);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $questions = $form->getData()->getQuestions();
            $this->santeHandler->handle($santeFiche, $questions);

            $this->dispatcher->dispatch(new SanteFicheUpdated($santeFiche->getId()));

            return $this->redirectToRoute('edr_parent_sante_fiche_show', [
                'uuid' => $enfant->getUuid(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdr/parent/sante_fiche/edit/etape3.html.twig',
            [
                'sante_fiche' => $santeFiche,
                'enfant' => $enfant,
                'form' => $form->createView(),
            ]
        );
    }
}
