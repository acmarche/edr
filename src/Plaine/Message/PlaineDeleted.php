<?php

namespace AcMarche\Edr\Plaine\Message;

final readonly class PlaineDeleted
{
    public function __construct(
        private int $plaineId
    ) {
    }

    public function getPlaineId(): int
    {
        return $this->plaineId;
    }
}
