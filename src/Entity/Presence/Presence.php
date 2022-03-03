<?php

namespace AcMarche\Edr\Entity\Presence;

use AcMarche\Edr\Contrat\Presence\PresenceInterface;
use AcMarche\Edr\Entity\Enfant;
use AcMarche\Edr\Entity\Jour;
use AcMarche\Edr\Entity\Security\Traits\UserAddTrait;
use AcMarche\Edr\Entity\Traits\AbsentTrait;
use AcMarche\Edr\Entity\Traits\ConfirmedTrait;
use AcMarche\Edr\Entity\Traits\EnfantTrait;
use AcMarche\Edr\Entity\Traits\HalfTrait;
use AcMarche\Edr\Entity\Traits\IdOldTrait;
use AcMarche\Edr\Entity\Traits\IdTrait;
use AcMarche\Edr\Entity\Traits\JourTrait;
use AcMarche\Edr\Entity\Traits\OrdreTrait;
use AcMarche\Edr\Entity\Traits\PaiementTrait;
use AcMarche\Edr\Entity\Traits\ReductionTrait;
use AcMarche\Edr\Entity\Traits\RemarqueTrait;
use AcMarche\Edr\Entity\Traits\TuteurTrait;
use AcMarche\Edr\Entity\Tuteur;
use AcMarche\Edr\Presence\Repository\PresenceRepository;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\UuidableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Knp\DoctrineBehaviors\Model\Uuidable\UuidableTrait;
use Stringable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Table(name: 'presence')]
#[ORM\UniqueConstraint(columns: ['jour_id', 'enfant_id'])]
#[ORM\Entity(repositoryClass: PresenceRepository::class)]
#[UniqueEntity(fields: ['jour', 'enfant'], message: "L'enfant est déjà inscrit à cette date")]
class Presence implements TimestampableInterface, PresenceInterface, UuidableInterface, Stringable
{
    use IdTrait;
    use UuidableTrait;
    use EnfantTrait;
    use TuteurTrait;
    use JourTrait;
    use AbsentTrait;
    use OrdreTrait;
    use ReductionTrait;
    use RemarqueTrait;
    use UserAddTrait;
    use TimestampableTrait;
    use HalfTrait;
    use ConfirmedTrait;
    use IdOldTrait;
    use PaiementTrait;

    #[ORM\ManyToOne(targetEntity: Tuteur::class, inversedBy: 'presences')]
    private ?Tuteur $tuteur = null;
    #[ORM\ManyToOne(targetEntity: Enfant::class, inversedBy: 'presences')]
    private ?Enfant $enfant = null;
    #[ORM\ManyToOne(targetEntity: Jour::class, inversedBy: 'presences')]
    private ?Jour $jour = null;
    /**
     * @var array|Enfant[]
     */
    public array $fratries = [];
    public int $ordreTmp = 0;

    public function __construct(Tuteur $tuteur, Enfant $enfant, Jour $jour)
    {
        $this->absent = 0;
        $this->half = 0;
        $this->tuteur = $tuteur;
        $this->enfant = $enfant;
        $this->jour = $jour;
    }

    public function __toString(): string
    {
        return 'presence to string';
    }
}
