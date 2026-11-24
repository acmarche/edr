<?php

namespace AcMarche\Edr\Entity\Traits;

use AcMarche\Edr\Entity\Relation;
use Doctrine\Common\Collections\Collection;

trait RelationsTrait
{
    /**
     * @var Relation[]
     */
    private Collection|array $relations = [];

    /**
     * @return Relation[]|Collection
     */
    public function getRelations(): Collection
    {
        return $this->relations;
    }

    /**
     * @param Relation[] $relations
     */
    public function setRelations(Collection $relations): void
    {
        $this->relations = $relations;
    }
}
