<?php

namespace AcMarche\Edr\Scolaire\Message;

final class AnneeScolaireUpdated
{
    public function __construct(
        private readonly int $anneeScolaireId
    ) {
    }

    public function getAnneeScolaireId(): int
    {
        return $this->anneeScolaireId;
    }
}
