<?php

namespace AcMarche\Edr\Enfant\Message;

final class EnfantCreated
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
