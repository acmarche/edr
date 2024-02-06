<?php

namespace AcMarche\Edr\Scolaire\Message;

final readonly class AnneeScolaireCreated
{
    public function __construct(
        private int $anneeScolaireId
    ) {
    }

    public function getAnneeScolaireId(): int
    {
        return $this->anneeScolaireId;
    }
}
