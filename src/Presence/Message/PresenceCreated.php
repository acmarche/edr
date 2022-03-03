<?php

namespace AcMarche\Edr\Presence\Message;

final class PresenceCreated
{
    public function __construct(
        private array $days
    ) {
    }

    public function getDays(): array
    {
        return $this->days;
    }
}
