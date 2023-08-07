<?php

namespace AcMarche\Edr\Controller\Admin;

use AcMarche\Edr\Entity\Sante\SanteQuestion;
use AcMarche\Edr\Sante\Form\SanteQuestionType;
use AcMarche\Edr\Sante\Message\SanteQuestionCreated;
use AcMarche\Edr\Sante\Message\SanteQuestionDeleted;
use AcMarche\Edr\Sante\Message\SanteQuestionUpdated;
use AcMarche\Edr\Sante\Repository\SanteQuestionRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/sante/question')]
#[IsGranted(data: 'ROLE_MERCREDI_ADMIN')]
final class SanteQuestionController extends AbstractController
{
    public function __construct(
        private readonly SanteQuestionRepository $santeQuestionRepository,
        private readonly MessageBusInterface $dispatcher
    ) {
    }

    #[Route(path: '/', name: 'edr_admin_sante_question_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render(
            '@AcMarcheEdrAdmin/sante_question/index.html.twig',
            [
                'sante_questions' => $this->santeQuestionRepository->findAllOrberByPosition(),
            ]
        );
    }

    #[Route(path: '/new', name: 'edr_admin_sante_question_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $santeQuestion = new SanteQuestion();
        $form = $this->createForm(SanteQuestionType::class, $santeQuestion);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->santeQuestionRepository->persist($santeQuestion);
            $this->santeQuestionRepository->flush();

            $this->dispatcher->dispatch(new SanteQuestionCreated($santeQuestion->getId()));

            return $this->redirectToRoute('edr_admin_sante_question_show', [
                'id' => $santeQuestion->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/sante_question/new.html.twig',
            [
                'sante_question' => $santeQuestion,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'edr_admin_sante_question_show', methods: ['GET'])]
    public function show(SanteQuestion $santeQuestion): Response
    {
        return $this->render(
            '@AcMarcheEdrAdmin/sante_question/show.html.twig',
            [
                'sante_question' => $santeQuestion,
            ]
        );
    }

    #[Route(path: '/{id}/edit', name: 'edr_admin_sante_question_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SanteQuestion $santeQuestion): Response
    {
        $form = $this->createForm(SanteQuestionType::class, $santeQuestion);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->santeQuestionRepository->flush();

            $this->dispatcher->dispatch(new SanteQuestionUpdated($santeQuestion->getId()));

            return $this->redirectToRoute('edr_admin_sante_question_show', [
                'id' => $santeQuestion->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/sante_question/edit.html.twig',
            [
                'sante_question' => $santeQuestion,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}/delete', name: 'edr_admin_sante_question_delete', methods: ['POST'])]
    public function delete(Request $request, SanteQuestion $santeQuestion): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete' . $santeQuestion->getId(), $request->request->get('_token'))) {
            $id = $santeQuestion->getId();
            $this->santeQuestionRepository->remove($santeQuestion);
            $this->santeQuestionRepository->flush();
            $this->dispatcher->dispatch(new SanteQuestionDeleted($id));
        }

        return $this->redirectToRoute('edr_admin_sante_question_index');
    }

    #[Route(path: '/q/sort', name: 'edr_admin_sante_question_sort', methods: ['GET'])]
    public function trier(): Response
    {
        $questions = $this->santeQuestionRepository->findAllOrberByPosition();

        return $this->render(
            '@AcMarcheEdrAdmin/sante_question/sort.html.twig',
            [
                'questions' => $questions,
            ]
        );
    }
}
