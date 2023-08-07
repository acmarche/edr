<?php

namespace AcMarche\Edr\Ecole\Message;

final class EcoleDeleted
{
    public function __construct(
        private readonly int $ecoleId
    ) {
    }

    public function getEcoleId(): int
    {
        return $this->ecoleId;
    }
}
