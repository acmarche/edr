<?php

namespace AcMarche\Edr\Controller\Admin;

use AcMarche\Edr\Document\Form\DocumentType;
use AcMarche\Edr\Document\Message\DocumentCreated;
use AcMarche\Edr\Document\Message\DocumentDeleted;
use AcMarche\Edr\Document\Message\DocumentUpdated;
use AcMarche\Edr\Document\Repository\DocumentRepository;
use AcMarche\Edr\Entity\Document;
use AcMarche\Edr\Entity\Page;
use AcMarche\Edr\Page\Repository\PageRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/document')]
#[IsGranted(data: 'ROLE_MERCREDI_ADMIN')]
final class DocumentController extends AbstractController
{
    public function __construct(
        private DocumentRepository $documentRepository,
        private PageRepository $pageRepository,
        private MessageBusInterface $dispatcher
    ) {
    }

    #[Route(path: '/', name: 'edr_admin_document_index', methods: ['GET', 'POST'])]
    public function index(): Response
    {
        $documents = $this->documentRepository->findAll();

        return $this->render(
            '@AcMarcheEdrAdmin/document/index.html.twig',
            [
                'documents' => $documents,
            ]
        );
    }

    #[Route(path: '/new/frompage/{id}', name: 'edr_admin_document_new_from_page', methods: ['GET', 'POST'])]
    public function new(Request $request, Page $page): Response
    {
        $document = new Document();
        $form = $this->createForm(DocumentType::class, $document);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->documentRepository->persist($document);
            $page->addDocument($document);
            $this->documentRepository->flush();
            $this->pageRepository->flush();

            $this->dispatcher->dispatch(new DocumentCreated($document->getId()));

            return $this->redirectToRoute('edr_admin_page_show', [
                'id' => $page->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/document/new.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'edr_admin_document_show', methods: ['GET'])]
    public function show(Document $document): Response
    {
        return $this->render(
            '@AcMarcheEdrAdmin/document/show.html.twig',
            [
                'document' => $document,
            ]
        );
    }

    #[Route(path: '/{id}/edit', name: 'edr_admin_document_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Document $document): Response
    {
        $form = $this->createForm(DocumentType::class, $document);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->documentRepository->flush();

            $this->dispatcher->dispatch(new DocumentUpdated($document->getId()));

            return $this->redirectToRoute('edr_admin_document_show', [
                'id' => $document->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/document/edit.html.twig',
            [
                'document' => $document,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}/delete', name: 'edr_admin_document_delete', methods: ['POST'])]
    public function delete(Request $request, Document $document): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$document->getId(), $request->request->get('_token'))) {
            $id = $document->getId();
            $this->documentRepository->remove($document);
            $this->documentRepository->flush();
            $this->dispatcher->dispatch(new DocumentDeleted($id));
        }

        return $this->redirectToRoute('edr_admin_document_index');
    }
}
