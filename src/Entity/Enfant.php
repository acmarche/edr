<?php

namespace AcMarche\Edr\Entity;

use AcMarche\Edr\Entity\Presence\Accueil;
use AcMarche\Edr\Entity\Presence\Presence;
use AcMarche\Edr\Entity\Sante\Traits\FicheSanteIsCompleteTrait;
use AcMarche\Edr\Entity\Sante\Traits\SanteFicheTrait;
use AcMarche\Edr\Entity\Scolaire\AnneeScolaire;
use AcMarche\Edr\Entity\Scolaire\Ecole;
use AcMarche\Edr\Entity\Scolaire\GroupeScolaire;
use AcMarche\Edr\Entity\Security\Traits\UserAddTrait;
use AcMarche\Edr\Entity\Traits\AccueilsTrait;
use AcMarche\Edr\Entity\Traits\AnneeScolaireTrait;
use AcMarche\Edr\Entity\Traits\ArchiveTrait;
use AcMarche\Edr\Entity\Traits\BirthdayTrait;
use AcMarche\Edr\Entity\Traits\EcoleTrait;
use AcMarche\Edr\Entity\Traits\EnfantNotesTrait;
use AcMarche\Edr\Entity\Traits\GroupeScolaireTrait;
use AcMarche\Edr\Entity\Traits\IdOldTrait;
use AcMarche\Edr\Entity\Traits\IdTrait;
use AcMarche\Edr\Entity\Traits\IsAccueilEcoleTrait;
use AcMarche\Edr\Entity\Traits\NomTrait;
use AcMarche\Edr\Entity\Traits\OrdreTrait;
use AcMarche\Edr\Entity\Traits\PhotoAutorisationTrait;
use AcMarche\Edr\Entity\Traits\PhotoTrait;
use AcMarche\Edr\Entity\Traits\PoidsTrait;
use AcMarche\Edr\Entity\Traits\PrenomTrait;
use AcMarche\Edr\Entity\Traits\PresencesTrait;
use AcMarche\Edr\Entity\Traits\RegistreNationalTrait;
use AcMarche\Edr\Entity\Traits\RelationsTrait;
use AcMarche\Edr\Entity\Traits\RemarqueTrait;
use AcMarche\Edr\Entity\Traits\SexeTrait;
use AcMarche\Edr\Entity\Traits\TelephonesTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\UuidableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Knp\DoctrineBehaviors\Model\Uuidable\UuidableTrait;
use Stringable;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[Vich\Uploadable]
#[ORM\Entity]
class Enfant implements SluggableInterface, TimestampableInterface, UuidableInterface, Stringable
{
    use IdTrait;
    use NomTrait;
    use PrenomTrait;
    use BirthdayTrait;
    use SexeTrait;
    use PhotoAutorisationTrait;
    use RemarqueTrait;
    use OrdreTrait;
    use PhotoTrait;
    use UserAddTrait;
    use SluggableTrait;
    use EcoleTrait;
    use RelationsTrait;
    use ArchiveTrait;
    use TimestampableTrait;
    use TelephonesTrait;
    use SanteFicheTrait;
    use FicheSanteIsCompleteTrait;
    use UuidableTrait;
    use GroupeScolaireTrait;
    use AnneeScolaireTrait;
    use PresencesTrait;
    use AccueilsTrait;
    use EnfantNotesTrait;
    use IsAccueilEcoleTrait;
    use RegistreNationalTrait;
    use PoidsTrait;
    use IdOldTrait;

    #[ORM\Column(type: 'boolean', nullable: false)]
    private bool $photo_autorisation = false;

    #[ORM\ManyToOne(targetEntity: AnneeScolaire::class, inversedBy: 'enfants')]
    private ?AnneeScolaire $annee_scolaire = null;

    #[ORM\ManyToOne(targetEntity: GroupeScolaire::class, inversedBy: 'enfants')]
    private ?GroupeScolaire $groupe_scolaire = null;

    #[ORM\ManyToOne(targetEntity: Ecole::class, inversedBy: 'enfants')]
    private ?Ecole $ecole = null;

    /**
     * @var Relation[]
     */
    #[ORM\OneToMany(targetEntity: Relation::class, mappedBy: 'enfant', cascade: ['remove'])]
    private Collection|array $relations = [];

    /**
     * J'ai mis la definition pour pouvoir mettre le cascade.
     *
     * @var Presence[]
     */
    #[ORM\OneToMany(targetEntity: Presence::class, mappedBy: 'enfant', cascade: ['remove'])]
    private Collection|array $presences = [];

    /**
     * J'ai mis la definition pour pouvoir mettre le cascade.
     *
     * @var Accueil[]
     */
    #[ORM\OneToMany(targetEntity: Accueil::class, mappedBy: 'enfant', cascade: ['remove'])]
    private Collection|array $accueils = [];

    public function __construct()
    {
        $this->relations = new ArrayCollection();
        $this->accueils = new ArrayCollection();
        $this->presences = new ArrayCollection();
        $this->notes = new ArrayCollection();
        $this->ficheSanteIsComplete = false;
    }

    public function __toString(): string
    {
        return mb_strtoupper((string) $this->nom, 'UTF-8') . ' ' . $this->prenom;
    }

    public function getSluggableFields(): array
    {
        return ['nom', 'prenom'];
    }

    public function shouldGenerateUniqueSlugs(): bool
    {
        return true;
    }

    public function getTuteurs(): array
    {
        return array_map(
            static fn ($relation) => $relation->getTuteur(),
            $this->getRelations()->toArray()
        );
    }
}
