<?php

namespace AcMarche\Edr\Jour\Message;

final readonly class JourDeleted
{
    public function __construct(
        private int $jourId
    ) {
    }

    public function getJourId(): int
    {
        return $this->jourId;
    }
}
