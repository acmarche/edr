<?php

namespace AcMarche\Edr\Facture\Message;

final class FactureUpdated
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
