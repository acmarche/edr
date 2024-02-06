<?php

namespace AcMarche\Edr\Sante\Message;

final readonly class SanteQuestionCreated
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
