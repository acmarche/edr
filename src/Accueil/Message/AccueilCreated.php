<?php

namespace AcMarche\Edr\Accueil\Message;

final readonly class AccueilCreated
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
