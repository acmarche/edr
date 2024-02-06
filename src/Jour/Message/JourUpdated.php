<?php

namespace AcMarche\Edr\Jour\Message;

final readonly class JourUpdated
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
