<?php

namespace AcMarche\Edr\Entity;

use AcMarche\Edr\Entity\Facture\CreancesTrait;
use AcMarche\Edr\Entity\Facture\Facture;
use AcMarche\Edr\Entity\Facture\FacturesTrait;
use AcMarche\Edr\Entity\Presence\Accueil;
use AcMarche\Edr\Entity\Security\Traits\UserAddTrait;
use AcMarche\Edr\Entity\Security\Traits\UsersTrait;
use AcMarche\Edr\Entity\Security\User;
use AcMarche\Edr\Entity\Traits\AccueilsTraits;
use AcMarche\Edr\Entity\Traits\AdresseTrait;
use AcMarche\Edr\Entity\Traits\ArchiveTrait;
use AcMarche\Edr\Entity\Traits\ConjointTrait;
use AcMarche\Edr\Entity\Traits\EmailTrait;
use AcMarche\Edr\Entity\Traits\IbanTrait;
use AcMarche\Edr\Entity\Traits\IdOldTrait;
use AcMarche\Edr\Entity\Traits\IdTrait;
use AcMarche\Edr\Entity\Traits\NomTrait;
use AcMarche\Edr\Entity\Traits\PapierTrait;
use AcMarche\Edr\Entity\Traits\PrenomTrait;
use AcMarche\Edr\Entity\Traits\PresencesTuteurTrait;
use AcMarche\Edr\Entity\Traits\RelationsTrait;
use AcMarche\Edr\Entity\Traits\RemarqueTrait;
use AcMarche\Edr\Entity\Traits\SexeTrait;
use AcMarche\Edr\Entity\Traits\TelephonieTrait;
use AcMarche\Edr\Tuteur\Repository\TuteurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Stringable;

#[ORM\Entity(repositoryClass: TuteurRepository::class)]
class Tuteur implements SluggableInterface, TimestampableInterface, Stringable
{
    use IdTrait;
    use NomTrait;
    use PrenomTrait;
    use AdresseTrait;
    use EmailTrait;
    use ConjointTrait;
    use RemarqueTrait;
    use SexeTrait;
    use TelephonieTrait;
    use SluggableTrait;
    use ArchiveTrait;
    use TimestampableTrait;
    use UserAddTrait;
    use UsersTrait;
    use PresencesTuteurTrait;
    use RelationsTrait;
    use FacturesTrait;
    use AccueilsTraits;
    use PapierTrait;
    use IbanTrait;
    use CreancesTrait;
    use IdOldTrait;

    /**
     * @var Relation[]
     */
    #[ORM\OneToMany(targetEntity: Relation::class, mappedBy: 'tuteur', cascade: ['remove'])]
    private Collection|array $relations = [];

    /**
     * J'ai mis la definition pour pouvoir mettre le cascade.
     *
     * @var Accueil[]|Collection
     */
    #[ORM\OneToMany(targetEntity: Accueil::class, mappedBy: 'tuteur', cascade: ['remove'])]
    private Collection $accueils;

    /**
     * @var Facture[]
     */
    #[ORM\OneToMany(targetEntity: Facture::class, mappedBy: 'tuteur', cascade: ['remove'])]
    private Collection|array $factures = [];

    /**
     * @var User[]|Collection
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'tuteurs')]
    private Collection $users;

    public function __construct()
    {
        $this->relations = new ArrayCollection();
        $this->presences = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->accueils = new ArrayCollection();
    }

    public function __toString(): string
    {
        return mb_strtoupper($this->nom, 'UTF-8') . ' ' . $this->prenom;
    }

    public function getSluggableFields(): array
    {
        return ['nom', 'prenom'];
    }

    public function shouldGenerateUniqueSlugs(): bool
    {
        return true;
    }
}
