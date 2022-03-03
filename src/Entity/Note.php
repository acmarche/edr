<?php

namespace AcMarche\Edr\Entity;

use AcMarche\Edr\Entity\Security\Traits\UserAddTrait;
use AcMarche\Edr\Entity\Traits\ArchiveTrait;
use AcMarche\Edr\Entity\Traits\EnfantTrait;
use AcMarche\Edr\Entity\Traits\IdTrait;
use AcMarche\Edr\Entity\Traits\RemarqueTrait;
use AcMarche\Edr\Note\Repository\NoteRepository;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Stringable;

#[ORM\Entity(repositoryClass: NoteRepository::class)]
class Note implements TimestampableInterface, Stringable
{
    use IdTrait;
    use RemarqueTrait;
    use TimestampableTrait;
    use ArchiveTrait;
    use UserAddTrait;
    use EnfantTrait;

    #[ORM\ManyToOne(targetEntity: Enfant::class, inversedBy: 'notes')]
    private ?Enfant $enfant = null;

    public function __construct(
        ?Enfant $enfant
    ) {
        $this->enfant = $enfant;
    }

    public function __toString(): string
    {
        return $this->getRemarque();
    }
}
