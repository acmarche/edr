<?php

namespace AcMarche\Edr\Controller\Admin;

use AcMarche\Edr\Entity\Page;
use AcMarche\Edr\Page\Form\PageType;
use AcMarche\Edr\Page\Message\PageCreated;
use AcMarche\Edr\Page\Message\PageDeleted;
use AcMarche\Edr\Page\Message\PageUpdated;
use AcMarche\Edr\Page\Repository\PageRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(path: '/page')]
#[IsGranted('ROLE_MERCREDI_ADMIN')]
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
                'slug' => $page->getSlug(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/page/new.html.twig',
            [
                'page' => $page,
                'form' => $form,
            ]
        );
    }

    /**
     * Image upload endpoint for the CKEditor SimpleUploadAdapter (config/packages/ckeditor5.yaml).
     * Expects the file in the "upload" field, answers {"url": ...} or {"error": {"message": ...}}.
     */
    #[Route(path: '/upload-image', name: 'edr_admin_page_upload_image', methods: ['POST'])]
    public function uploadImage(
        Request $request,
        ValidatorInterface $validator,
        #[Autowire('%kernel.project_dir%/public/files/pages')] string $uploadDirectory,
    ): JsonResponse {
        $file = $request->files->get('upload');
        if (!$file instanceof UploadedFile) {
            return $this->json(['error' => ['message' => 'Aucun fichier reçu.']], Response::HTTP_BAD_REQUEST);
        }

        $violations = $validator->validate($file, new Image(
            maxSize: '5M',
            mimeTypes: ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
            mimeTypesMessage: 'Formats acceptés : JPEG, PNG, GIF, WebP.',
        ));
        if (\count($violations) > 0) {
            return $this->json(['error' => ['message' => $violations[0]->getMessage()]], Response::HTTP_BAD_REQUEST);
        }

        $fileName = bin2hex(random_bytes(12)).'.'.$file->guessExtension();
        try {
            $file->move($uploadDirectory, $fileName);
        } catch (FileException) {
            return $this->json(['error' => ['message' => 'Impossible d\'enregistrer l\'image.']], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json(['url' => $request->getBasePath().'/files/pages/'.$fileName]);
    }

    #[Route(path: '/{slug}', name: 'edr_admin_page_show', methods: ['GET'])]
    public function show(
        #[MapEntity(expr: 'repository.findOneBySlug(slug)')] Page $page): Response
    {
        return $this->render(
            '@AcMarcheEdrAdmin/page/show.html.twig',
            [
                'page' => $page,
            ]
        );
    }

    #[Route(path: '/{slug}/edit', name: 'edr_admin_page_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request,
        #[MapEntity(expr: 'repository.findOneBySlug(slug)')] Page $page): Response
    {
        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->pageRepository->flush();

            $this->dispatcher->dispatch(new PageUpdated($page->getId()));

            return $this->redirectToRoute('edr_admin_page_show', [
                'slug' => $page->getSlug(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/page/edit.html.twig',
            [
                'page' => $page,
                'form' => $form,
            ]
        );
    }

    #[Route(path: '/{slug}/delete', name: 'edr_admin_page_delete', methods: ['POST'])]
    public function delete(Request $request,
        #[MapEntity(expr: 'repository.findOneBySlug(slug)')] Page $page): RedirectResponse
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
