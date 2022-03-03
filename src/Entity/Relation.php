<?php

namespace AcMarche\Edr\Entity;

use AcMarche\Edr\Entity\Traits\EnfantTrait;
use AcMarche\Edr\Entity\Traits\IdTrait;
use AcMarche\Edr\Entity\Traits\OrdreTrait;
use AcMarche\Edr\Entity\Traits\TuteurTrait;
use AcMarche\Edr\Relation\Repository\RelationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Table(name: 'relation')]
#[ORM\UniqueConstraint(columns: ['tuteur_id', 'enfant_id'])]
#[ORM\Entity(repositoryClass: RelationRepository::class)]
#[UniqueEntity(fields: ['tuteur', 'enfant'], message: 'Cet enfant est déjà lié à ce parent')]
class Relation
{
    use IdTrait;
    use TuteurTrait;
    use EnfantTrait;
    use OrdreTrait;

    #[ORM\ManyToOne(targetEntity: Tuteur::class, inversedBy: 'relations', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Tuteur $tuteur=null;

    #[ORM\ManyToOne(targetEntity: Enfant::class, inversedBy: 'relations', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Enfant $enfant=null;

    #[ORM\Column(type: 'string', length: 200, nullable: true, options: [
        'comment' => 'pere,mere,beau pere..',
    ])]
    private ?string $type = null;

    public function __construct(
        ?Tuteur $tuteur,
        ?Enfant $enfant
    ) {
        $this->tuteur = $tuteur;
        $this->enfant = $enfant;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }
}
