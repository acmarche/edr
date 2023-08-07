<?php

namespace AcMarche\Edr\Relation\Message;

final class RelationCreated
{
    public function __construct(
        private readonly int $relationId
    ) {
    }

    public function getRelationId(): int
    {
        return $this->relationId;
    }
}
