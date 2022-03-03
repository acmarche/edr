<?php

namespace AcMarche\Edr\Tuteur\Message;

final class TuteurDeleted
{
    public function __construct(
        private int $tuteurId
    ) {
    }

    public function getTuteurId(): int
    {
        return $this->tuteurId;
    }
}
