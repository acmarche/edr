<?php

namespace AcMarche\Edr\Accueil\Message;

final readonly class AccueilUpdated
{
    public function __construct(
        private int $accueilId
    ) {
    }

    public function getAccueilId(): int
    {
        return $this->accueilId;
    }
}
