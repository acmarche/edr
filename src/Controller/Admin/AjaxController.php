<?php

namespace AcMarche\Edr\Controller\Admin;

use AcMarche\Edr\Enfant\Repository\EnfantRepository;
use AcMarche\Edr\Page\Repository\PageRepository;
use AcMarche\Edr\Sante\Repository\SanteQuestionRepository;
use AcMarche\Edr\Tuteur\Repository\TuteurRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[IsGranted(data: 'ROLE_MERCREDI_ADMIN')]
#[Route(path: '/ajax')]
final class AjaxController extends AbstractController
{
    public function __construct(
        private EnfantRepository $enfantRepository,
        private TuteurRepository $tuteurRepository,
        private SanteQuestionRepository $santeQuestionRepository,
        private PageRepository $pageRepository
    ) {
    }

    /**
     * not use.
     */
    #[Route(path: '/tuteurs', name: 'edr_admin_ajax_tuteurs')]
    public function tuteurs(Request $request): Response
    {
        $keyword = $request->get('q');
        $tuteurs = [];
        if ($keyword) {
            $tuteurs = $this->tuteurRepository->search($keyword);
        }

        return $this->render('@AcMarcheEdr/commun/tuteur/_list.html.twig', [
            'tuteurs' => $tuteurs,
        ]);
    }

    #[Route(path: '/enfants/link', name: 'edr_admin_ajax_enfants', methods: ['GET'])]
    public function enfants(Request $request): Response
    {
        $keyword = $request->get('q');
        $enfants = [];
        if ($keyword) {
            $enfants = $this->enfantRepository->findByName($keyword, true, 10);
        }

        return $this->render('@AcMarcheEdr/commun/enfant/_list.html.twig', [
            'enfants' => $enfants,
        ]);
    }

    #[Route(path: '/enfants/nolink', name: 'edr_admin_ajax_enfants_no_link', methods: ['GET'])]
    public function enfantsNoLink(Request $request): Response
    {
        $keyword = $request->get('q');
        $enfants = [];
        if ($keyword) {
            $enfants = $this->enfantRepository->findByName($keyword, true, 10);
        }

        return $this->render('@AcMarcheEdr/commun/enfant/_list_not_link.html.twig', [
            'enfants' => $enfants,
        ]);
    }

    /**
     * not use.
     */
    #[Route(path: '/plaine/date', name: 'edr_admin_ajax_plaine_new_date')]
    public function plaineDate(Request $request): Response
    {
        $index = $request->get('index', 0);

        return $this->render('@AcMarcheEdrAdmin/plaine/_new_line.html.twig', [
            'index' => $index,
        ]);
    }

    #[Route(path: '/q/sort/', name: 'edr_admin_ajax_question_sort', methods: ['POST'])]
    public function trierQuestion(Request $request): JsonResponse
    {
        //    $isAjax = $request->isXmlHttpRequest();
        //    if ($isAjax) {
        //
        $data = json_decode($request->getContent(), null, 512, JSON_THROW_ON_ERROR);
        $questions = $data->questions;
        foreach ($questions as $position => $questionId) {
            $question = $this->santeQuestionRepository->find($questionId);
            if (null !== $question) {
                $question->setDisplayOrder($position);
            }
        }
        $this->santeQuestionRepository->flush();

        return $this->json('<div class="alert alert-success">Tri enregistré</div>');
    }

    #[Route(path: '/q/sort/{id}', name: 'edr_admin_ajax_page_sort', methods: ['POST', 'PATCH'])]
    public function trierPage(Request $request, int $id): Response
    {
        $isAjax = $request->isXmlHttpRequest();
        if ($isAjax) {
            $position = $request->request->get('position');
            if (($page = $this->pageRepository->find($id)) !== null) {
                $page->setPosition($position);
                $this->pageRepository->flush();
            }

            return new Response('<div class="alert alert-success">Tri enregistré '.$position.'</div>');
        }

        return new Response('<div class="alert alert-danger">Faill</div>');
    }
}
