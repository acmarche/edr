<?php

namespace AcMarche\Edr\Facture\Message;

final class FacturesCreated
{
    public function __construct(
        private readonly array $factureIds
    ) {
    }

    public function getFactureIds(): array
    {
        return $this->factureIds;
    }
}
