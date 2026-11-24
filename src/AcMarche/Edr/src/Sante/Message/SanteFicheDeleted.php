<?php

namespace AcMarche\Edr\Sante\Message;

final readonly class SanteFicheDeleted
{
    public function __construct(
        private int $santeFicheId
    ) {
    }

    public function getSanteFicheId(): int
    {
        return $this->santeFicheId;
    }
}
