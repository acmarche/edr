<?php

namespace AcMarche\Edr\Relation\Message;

final class RelationUpdated
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
