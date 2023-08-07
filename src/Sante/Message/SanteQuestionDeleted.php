<?php

namespace AcMarche\Edr\Sante\Message;

final class SanteQuestionDeleted
{
    public function __construct(
        private readonly int $santeQuestionId
    ) {
    }

    public function getSanteQuestionId(): int
    {
        return $this->santeQuestionId;
    }
}
