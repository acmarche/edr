<?php

namespace AcMarche\Edr\Sante\Message;

final readonly class SanteQuestionUpdated
{
    public function __construct(
        private int $santeQuestionId
    ) {
    }

    public function getSanteQuestionId(): int
    {
        return $this->santeQuestionId;
    }
}
