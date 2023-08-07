<?php

namespace AcMarche\Edr\Presence\Message;

final class PresenceUpdated
{
    public function __construct(
        private readonly int $presenceId
    ) {
    }

    public function getPresenceId(): int
    {
        return $this->presenceId;
    }
}
