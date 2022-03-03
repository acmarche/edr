<?php

namespace AcMarche\Edr\Plaine\Message;

final class PlaineUpdated
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
