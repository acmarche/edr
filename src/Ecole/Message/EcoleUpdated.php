<?php

namespace AcMarche\Edr\Ecole\Message;

final class EcoleUpdated
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
