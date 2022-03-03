<?php

namespace AcMarche\Edr\Accueil\Message;

final class AccueilUpdated
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
