<?php

namespace AcMarche\Edr\Facture\Message;

final class FactureUpdated
{
    public function __construct(
        private readonly int $factureId
    ) {
    }

    public function getFactureId(): int
    {
        return $this->factureId;
    }
}
