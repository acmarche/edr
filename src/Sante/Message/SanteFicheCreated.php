<?php

namespace AcMarche\Edr\Sante\Message;

final class SanteFicheCreated
{
    public function __construct(
        private readonly int $santeFicheId
    ) {
    }

    public function getSanteFicheId(): int
    {
        return $this->santeFicheId;
    }
}
