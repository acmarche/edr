<?php

namespace AcMarche\Edr\Facture\Message;

final readonly class FacturesCreated
{
    public function __construct(
        private array $factureIds
    ) {
    }

    public function getFactureIds(): array
    {
        return $this->factureIds;
    }
}
