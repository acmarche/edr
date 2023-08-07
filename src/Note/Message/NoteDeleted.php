<?php

namespace AcMarche\Edr\Note\Message;

final class NoteDeleted
{
    public function __construct(
        private readonly int $noteId
    ) {
    }

    public function getNoteId(): int
    {
        return $this->noteId;
    }
}
