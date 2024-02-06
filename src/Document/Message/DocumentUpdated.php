<?php

namespace AcMarche\Edr\Document\Message;

final readonly class DocumentUpdated
{
    public function __construct(
        private int $documentId
    ) {
    }

    public function getDocumentId(): int
    {
        return $this->documentId;
    }
}
