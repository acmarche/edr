<?php

namespace AcMarche\Edr\Scolaire\Message;

final readonly class GroupeScolaireDeleted
{
    public function __construct(
        private int $groupeScolaireId
    ) {
    }

    public function getGroupeScolaireId(): int
    {
        return $this->groupeScolaireId;
    }
}
