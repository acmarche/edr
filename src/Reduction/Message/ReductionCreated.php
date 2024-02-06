<?php

namespace AcMarche\Edr\Reduction\Message;

final readonly class ReductionCreated
{
    public function __construct(
        private int $ecoleId
    ) {
    }

    public function getReductionId(): int
    {
        return $this->ecoleId;
    }
}
