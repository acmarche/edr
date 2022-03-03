<?php

namespace AcMarche\Edr\Document\Message;

final class DocumentCreated
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
