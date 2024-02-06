<?php

namespace AcMarche\Edr\Facture\Message;

final readonly class FactureCreated
{
    public function __construct(
        private int $factureId
    ) {
    }

    public function getFactureId(): int
    {
        return $this->factureId;
    }
}
