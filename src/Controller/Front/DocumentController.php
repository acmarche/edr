<?php

namespace AcMarche\Edr\Controller\Front;

use AcMarche\Edr\Entity\Document;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Handler\DownloadHandler;

#[Route(path: '/document')]
final class DocumentController extends AbstractController
{
    public function __construct(
        private DownloadHandler $downloadHandler
    ) {
    }

    #[Route(path: '/{id}', name: 'edr_font_document_download')]
    public function index(Document $document): StreamedResponse
    {
        return $this->downloadHandler->downloadObject($document, 'file');
    }
}
