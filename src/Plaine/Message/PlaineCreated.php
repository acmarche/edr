<?php

namespace AcMarche\Edr\Plaine\Message;

final class PlaineCreated
{
    public function __construct(
        private readonly int $plaineId
    ) {
    }

    public function getPlaineId(): int
    {
        return $this->plaineId;
    }
}
