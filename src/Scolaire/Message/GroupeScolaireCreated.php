<?php

namespace AcMarche\Edr\Scolaire\Message;

final readonly class GroupeScolaireCreated
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
