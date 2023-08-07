<?php

namespace AcMarche\Edr\User\Message;

final class UserDeleted
{
    public function __construct(
        private readonly int $userId
    ) {
    }

    public function getUserId(): int
    {
        return $this->userId;
    }
}
