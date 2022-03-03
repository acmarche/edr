<?php

namespace AcMarche\Edr\Enfant\Handler;

use AcMarche\Edr\Enfant\Repository\EnfantRepository;
use AcMarche\Edr\Entity\Enfant;
use AcMarche\Edr\Entity\Relation;
use AcMarche\Edr\Entity\Tuteur;
use AcMarche\Edr\Relation\Repository\RelationRepository;

final class EnfantHandler
{
    public function __construct(
        private EnfantRepository $enfantRepository,
        private RelationRepository $relationRepository
    ) {
    }

    public function newHandle(Enfant $enfant, Tuteur $tuteur): void
    {
        $this->enfantRepository->persist($enfant);
        $relation = new Relation($tuteur, $enfant);
        $this->relationRepository->persist($relation);
        $this->enfantRepository->flush();
        $this->relationRepository->flush();
    }
}
