<?php

namespace AcMarche\Edr\Ecole\Message;

final readonly class EcoleUpdated
{
    public function __construct(
        private int $ecoleId
    ) {
    }

    public function getEcoleId(): int
    {
        return $this->ecoleId;
    }
}
