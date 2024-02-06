<?php

namespace AcMarche\Edr\User\Message;

final readonly class UserCreated
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
