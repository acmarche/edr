<?php

namespace AcMarche\Edr\Scolaire\Message;

final class GroupeScolaireCreated
{
    public function __construct(
        private readonly int $groupeScolaireId
    ) {
    }

    public function getGroupeScolaireId(): int
    {
        return $this->groupeScolaireId;
    }
}
