<?php

namespace AcMarche\Edr\Registration\Message;

final readonly class RegisterCreated
{
    public function __construct(
        private int $userId
    ) {
    }

    public function getUserId(): int
    {
        return $this->userId;
    }
}
