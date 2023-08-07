<?php

namespace AcMarche\Edr\Controller\Admin;

use AcMarche\Edr\Entity\Page;
use AcMarche\Edr\Page\Form\PageType;
use AcMarche\Edr\Page\Message\PageCreated;
use AcMarche\Edr\Page\Message\PageDeleted;
use AcMarche\Edr\Page\Message\PageUpdated;
use AcMarche\Edr\Page\Repository\PageRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/page')]
#[IsGranted(data: 'ROLE_MERCREDI_ADMIN')]
final class PageController extends AbstractController
{
    public function __construct(
        private readonly PageRepository $pageRepository,
        private readonly MessageBusInterface $dispatcher
    ) {
    }

    #[Route(path: '/', name: 'edr_admin_page_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render(
            '@AcMarcheEdrAdmin/page/index.html.twig',
            [
                'pages' => $this->pageRepository->findAll(),
            ]
        );
    }

    #[Route(path: '/new', name: 'edr_admin_page_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $page = new Page();
        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->pageRepository->persist($page);
            $this->pageRepository->flush();

            $this->dispatcher->dispatch(new PageCreated($page->getId()));

            return $this->redirectToRoute('edr_admin_page_show', [
                'id' => $page->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/page/new.html.twig',
            [
                'page' => $page,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'edr_admin_page_show', methods: ['GET'])]
    public function show(Page $page): Response
    {
        return $this->render(
            '@AcMarcheEdrAdmin/page/show.html.twig',
            [
                'page' => $page,
            ]
        );
    }

    #[Route(path: '/{id}/edit', name: 'edr_admin_page_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Page $page): Response
    {
        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->pageRepository->flush();

            $this->dispatcher->dispatch(new PageUpdated($page->getId()));

            return $this->redirectToRoute('edr_admin_page_show', [
                'id' => $page->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/page/edit.html.twig',
            [
                'page' => $page,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}/delete', name: 'edr_admin_page_delete', methods: ['POST'])]
    public function delete(Request $request, Page $page): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete' . $page->getId(), $request->request->get('_token'))) {
            $pageId = $page->getId();
            $this->pageRepository->remove($page);
            $this->pageRepository->flush();
            $this->dispatcher->dispatch(new PageDeleted($pageId));
        }

        return $this->redirectToRoute('edr_admin_page_index');
    }

    #[Route(path: '/s/sort', name: 'edr_admin_page_sort', methods: ['GET'])]
    public function trier(): Response
    {
        $pages = $this->pageRepository->findAll();

        return $this->render(
            '@AcMarcheEdrAdmin/page/sort.html.twig',
            [
                'pages' => $pages,
            ]
        );
    }
}
