<?php

namespace AcMarche\Edr\Tuteur\Message;

final readonly class TuteurCreated
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
