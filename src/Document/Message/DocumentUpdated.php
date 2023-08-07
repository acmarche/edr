<?php

namespace AcMarche\Edr\Document\Message;

final class DocumentUpdated
{
    public function __construct(
        private readonly int $documentId
    ) {
    }

    public function getDocumentId(): int
    {
        return $this->documentId;
    }
}
