<?php

namespace AcMarche\Edr\Relation\Utils;

use AcMarche\Edr\Entity\Enfant;
use AcMarche\Edr\Entity\Relation;
use AcMarche\Edr\Entity\Tuteur;
use AcMarche\Edr\Relation\Repository\RelationRepository;

final readonly class RelationUtils
{
    public function __construct(
        private RelationRepository $relationRepository
    ) {
    }

    /**
     * @return Enfant[]
     */
    public function findEnfantsByTuteur(Tuteur $tuteur): array
    {
        $relations = $this->relationRepository->findByTuteur($tuteur);

        return self::extractEnfants($relations);
    }

    /**
     * @param Relation[] $relations
     *
     * @return Tuteur[]
     */
    public static function extractTuteurs(array $relations): array
    {
        return array_unique(
            array_map(
                static fn ($relation) => $relation->getTuteur(),
                $relations
            )
        );
    }

    /**
     * @param Relation[] $relations
     *
     * @return Enfant[]
     */
    public static function extractEnfants(array $relations): array
    {
        return array_unique(
            array_map(
                static fn ($relation) => $relation->getEnfant(),
                $relations
            )
        );
    }
}
