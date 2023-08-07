<?php

namespace AcMarche\Edr\Entity;

use DateTimeInterface;
use AcMarche\Edr\Entity\Plaine\Plaine;
use AcMarche\Edr\Entity\Plaine\PlaineTrait;
use AcMarche\Edr\Entity\Presence\Presence;
use AcMarche\Edr\Entity\Scolaire\Ecole;
use AcMarche\Edr\Entity\Traits\AnimateursTrait;
use AcMarche\Edr\Entity\Traits\ArchiveTrait;
use AcMarche\Edr\Entity\Traits\ColorTrait;
use AcMarche\Edr\Entity\Traits\ForfaitTrait;
use AcMarche\Edr\Entity\Traits\IdOldTrait;
use AcMarche\Edr\Entity\Traits\IdTrait;
use AcMarche\Edr\Entity\Traits\PedagogiqueTrait;
use AcMarche\Edr\Entity\Traits\PrixTrait;
use AcMarche\Edr\Entity\Traits\RemarqueTrait;
use AcMarche\Edr\Jour\Repository\JourRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Stringable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Table]
#[ORM\UniqueConstraint(columns: ['date_jour', 'pedagogique', 'plaine_id'])]
#[ORM\Entity(repositoryClass: JourRepository::class)]
#[UniqueEntity(fields: ['date_jour', 'pedagogique', 'plaine'], message: 'Cette valeur existe déjà')]
class Jour implements TimestampableInterface, Stringable
{
    use IdTrait;
    use TimestampableTrait;
    use PrixTrait;
    use ColorTrait;
    use RemarqueTrait;
    use ArchiveTrait;
    use PedagogiqueTrait;
    use ForfaitTrait;
    use AnimateursTrait;
    use PlaineTrait;
    use IdOldTrait;

    /**
     * J'ai mis la definition pour pouvoir mettre le cascade.
     *
     * @var Presence[]
     */
    #[ORM\OneToMany(targetEntity: Presence::class, mappedBy: 'jour', cascade: ['remove'])]
    private Collection|array $presences = [];

    #[ORM\ManyToOne(targetEntity: Plaine::class, inversedBy: 'jours')]
    private ?Plaine $plaine = null;

    /**
     * @var Animateur[]
     */
    #[ORM\ManyToMany(targetEntity: Animateur::class, mappedBy: 'jours')]
    private Collection|array $animateurs = [];

    #[ORM\ManyToMany(targetEntity: Ecole::class)]
    private Collection $ecoles;

    public function __construct(
        #[ORM\Column(name: 'date_jour', type: 'date')]
        private ?DateTimeInterface $date_jour
    ) {
        $this->presences = new ArrayCollection();
        $this->animateurs = new ArrayCollection();
        $this->ecoles = new ArrayCollection();
        $this->prix1 = 0;
        $this->prix2 = 0;
        $this->prix3 = 0;
        $this->forfait = 0;
        $this->pedagogique = false;
    }

    public function __toString(): string
    {
        return $this->date_jour->format('d-m-Y');
    }

    public function getDateJour(): ?DateTimeInterface
    {
        return $this->date_jour;
    }

    public function setDateJour(DateTimeInterface $date_jour): self
    {
        $this->date_jour = $date_jour;

        return $this;
    }

    /**
     * @return Collection|Presence[]
     */
    public function getPresences(): Collection
    {
        return $this->presences;
    }

    public function addPresence(Presence $presence): self
    {
        if (!$this->presences->contains($presence)) {
            $this->presences[] = $presence;
            $presence->setJour($this);
        }

        return $this;
    }

    public function removePresence(Presence $presence): self
    {
        // set the owning side to null (unless already changed)
        if ($this->presences->removeElement($presence) && $presence->getJour() === $this) {
            $presence->setJour(null);
        }

        return $this;
    }

    /**
     * @return Collection|Ecole[]
     */
    public function getEcoles(): Collection
    {
        return $this->ecoles;
    }

    public function addEcole(Ecole $ecole): self
    {
        if (!$this->ecoles->contains($ecole)) {
            $this->ecoles[] = $ecole;
        }

        return $this;
    }

    public function removeEcole(Ecole $ecole): self
    {
        $this->ecoles->removeElement($ecole);

        return $this;
    }
}
