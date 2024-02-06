<?php

namespace AcMarche\Edr\Presence\Message;

final readonly class PresenceUpdated
{
    public function __construct(
        private int $presenceId
    ) {
    }

    public function getPresenceId(): int
    {
        return $this->presenceId;
    }
}
