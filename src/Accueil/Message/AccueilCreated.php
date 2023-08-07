<?php

namespace AcMarche\Edr\Accueil\Message;

final class AccueilCreated
{
    public function __construct(
        private readonly int $accueilId
    ) {
    }

    public function getAccueilId(): int
    {
        return $this->accueilId;
    }
}
