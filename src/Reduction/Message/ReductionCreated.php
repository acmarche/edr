<?php

namespace AcMarche\Edr\Reduction\Message;

final class ReductionCreated
{
    public function __construct(
        private readonly int $ecoleId
    ) {
    }

    public function getReductionId(): int
    {
        return $this->ecoleId;
    }
}
