<?php

namespace AcMarche\Edr\Facture\Message;

final class FactureDeleted
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
