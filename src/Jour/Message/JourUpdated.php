<?php

namespace AcMarche\Edr\Jour\Message;

final class JourUpdated
{
    public function __construct(
        private readonly int $jourId
    ) {
    }

    public function getJourId(): int
    {
        return $this->jourId;
    }
}
