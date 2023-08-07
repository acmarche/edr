<?php

namespace AcMarche\Edr\Relation;

use AcMarche\Edr\Enfant\Repository\EnfantRepository;
use AcMarche\Edr\Entity\Enfant;
use AcMarche\Edr\Entity\Relation;
use AcMarche\Edr\Entity\Tuteur;
use AcMarche\Edr\Relation\Repository\RelationRepository;
use Exception;

final class RelationHandler
{
    public function __construct(
        private readonly RelationRepository $relationRepository,
        private readonly EnfantRepository $enfantRepository
    ) {
    }

    /**
     * @throws Exception
     */
    public function handleAttachEnfant(Tuteur $tuteur, ?int $enfantId): Relation
    {
        if (!$enfantId) {
            throw new Exception('Enfant non trouvé');
        }

        $enfant = $this->enfantRepository->find($enfantId);
        if (!$enfant instanceof Enfant) {
            throw new Exception('Enfant non trouvé');
        }

        if ($this->relationRepository->findOneByTuteurAndEnfant($tuteur, $enfant) instanceof Relation) {
            throw new Exception('L\'enfant est déjà lié à ce parent');
        }

        $relation = new Relation($tuteur, $enfant);
        $this->relationRepository->persist($relation);
        $this->relationRepository->flush();

        return $relation;
    }
}
