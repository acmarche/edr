<?php

namespace AcMarche\Edr\Note\Message;

final readonly class NoteDeleted
{
    public function __construct(
        private int $noteId
    ) {
    }

    public function getNoteId(): int
    {
        return $this->noteId;
    }
}
