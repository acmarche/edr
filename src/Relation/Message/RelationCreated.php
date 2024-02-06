<?php

namespace AcMarche\Edr\Relation\Message;

final readonly class RelationCreated
{
    public function __construct(
        private int $relationId
    ) {
    }

    public function getRelationId(): int
    {
        return $this->relationId;
    }
}
