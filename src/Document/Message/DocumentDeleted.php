<?php

namespace AcMarche\Edr\Document\Message;

final readonly class DocumentDeleted
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
