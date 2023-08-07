<?php

namespace AcMarche\Edr\Entity\Scolaire;

use AcMarche\Edr\Entity\Enfant;
use AcMarche\Edr\Entity\Traits\IdTrait;
use AcMarche\Edr\Entity\Traits\NomTrait;
use AcMarche\Edr\Entity\Traits\RemarqueTrait;
use AcMarche\Edr\Scolaire\Repository\AnneeScolaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Stringable;

#[ORM\Entity(repositoryClass: AnneeScolaireRepository::class)]
class AnneeScolaire implements Stringable
{
    use IdTrait;
    use NomTrait;
    use RemarqueTrait;
    #[ORM\OneToOne(targetEntity: self::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL', unique: true)]
    private ?AnneeScolaire $annee_suivante = null;

    #[ORM\Column(type: 'integer')]
    private ?int $ordre = null;

    /**
     * @var Enfant[]
     */
    #[ORM\OneToMany(targetEntity: Enfant::class, mappedBy: 'annee_scolaire')]
    private Collection|array $enfants = [];

    #[ORM\ManyToOne(targetEntity: GroupeScolaire::class, inversedBy: 'annees_scolaires')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?GroupeScolaire $groupe_scolaire = null;

    public function __construct()
    {
        $this->enfants = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->nom;
    }

    /**
     * @return Collection|Enfant[]
     */
    public function getEnfants(): Collection
    {
        return $this->enfants;
    }

    public function addEnfant(Enfant $enfant): self
    {
        if (! $this->enfants->contains($enfant)) {
            $this->enfants[] = $enfant;
            $enfant->setAnneeScolaire($this);
        }

        return $this;
    }

    public function removeEnfant(Enfant $enfant): self
    {
        // set the owning side to null (unless already changed)
        if ($this->enfants->removeElement($enfant) && $enfant->getAnneeScolaire() === $this) {
            $enfant->setAnneeScolaire(null);
        }

        return $this;
    }

    public function getGroupeScolaire(): ?GroupeScolaire
    {
        return $this->groupe_scolaire;
    }

    public function setGroupeScolaire(?GroupeScolaire $groupe_scolaire): self
    {
        $this->groupe_scolaire = $groupe_scolaire;

        return $this;
    }

    public function getOrdre(): ?int
    {
        return $this->ordre;
    }

    public function setOrdre(int $ordre): self
    {
        $this->ordre = $ordre;

        return $this;
    }

    public function getAnneeSuivante(): ?self
    {
        return $this->annee_suivante;
    }

    public function setAnneeSuivante(?self $annee_suivante): self
    {
        $this->annee_suivante = $annee_suivante;

        return $this;
    }
}
