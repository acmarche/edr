<?php

namespace AcMarche\Edr\Enfant\Message;

final class EnfantUpdated
{
    public function __construct(
        private readonly int $enfantId
    ) {
    }

    public function getEnfantId(): int
    {
        return $this->enfantId;
    }
}
