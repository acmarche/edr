<?php

namespace AcMarche\Edr\Entity;

use AcMarche\Edr\Animateur\Repository\AnimateurRepository;
use AcMarche\Edr\Entity\Security\Traits\UserAddTrait;
use AcMarche\Edr\Entity\Security\Traits\UsersTrait;
use AcMarche\Edr\Entity\Security\User;
use AcMarche\Edr\Entity\Traits\AdresseTrait;
use AcMarche\Edr\Entity\Traits\ArchiveTrait;
use AcMarche\Edr\Entity\Traits\EmailTrait;
use AcMarche\Edr\Entity\Traits\IdTrait;
use AcMarche\Edr\Entity\Traits\JoursTrait;
use AcMarche\Edr\Entity\Traits\NomTrait;
use AcMarche\Edr\Entity\Traits\PrenomTrait;
use AcMarche\Edr\Entity\Traits\RemarqueTrait;
use AcMarche\Edr\Entity\Traits\SexeTrait;
use AcMarche\Edr\Entity\Traits\TelephonieTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Stringable;

#[ORM\Entity(repositoryClass: AnimateurRepository::class)]
class Animateur implements TimestampableInterface, Stringable
{
    use IdTrait;
    use NomTrait;
    use PrenomTrait;
    use AdresseTrait;
    use EmailTrait;
    use RemarqueTrait;
    use SexeTrait;
    use TelephonieTrait;
    use ArchiveTrait;
    use TimestampableTrait;
    use UserAddTrait;
    use UsersTrait;
    use JoursTrait;
    /**
     * @var User[]|Collection
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'animateurs')]
    private Collection $users;

    /**
     * @var Jour[]|Collection
     */
    #[ORM\ManyToMany(targetEntity: Jour::class, inversedBy: 'animateurs')]
    private Collection $jours;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->jours = new ArrayCollection();
    }

    public function __toString(): string
    {
        return mb_strtoupper($this->nom, 'UTF-8') . ' ' . $this->prenom;
    }
}
