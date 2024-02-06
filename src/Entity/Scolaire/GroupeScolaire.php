<?php

namespace AcMarche\Edr\Entity\Scolaire;

use AcMarche\Edr\Entity\Enfant;
use AcMarche\Edr\Entity\Plaine\PlaineGroupe;
use AcMarche\Edr\Entity\Traits\IdTrait;
use AcMarche\Edr\Entity\Traits\NomTrait;
use AcMarche\Edr\Entity\Traits\OrdreTrait;
use AcMarche\Edr\Entity\Traits\RemarqueTrait;
use AcMarche\Edr\Scolaire\Repository\GroupeScolaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Stringable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table]
#[ORM\UniqueConstraint(columns: ['age_minimum', 'is_plaine'])]
#[ORM\UniqueConstraint(columns: ['age_maximum', 'is_plaine'])]
#[ORM\Entity(repositoryClass: GroupeScolaireRepository::class)]
#[UniqueEntity(fields: ['age_minimum', 'is_plaine'], message: 'Déjà un groupe plaine avec cette âge minimum')]
#[UniqueEntity(fields: ['age_maximum', 'is_plaine'], message: 'Déjà un groupe plaine avec cette âge maximum')]
class GroupeScolaire implements Stringable
{
    use IdTrait;
    use NomTrait;
    use RemarqueTrait;
    use OrdreTrait;
    #[ORM\Column(type: 'decimal', precision: 3, scale: 1, nullable: true)]
    #[Assert\LessThan(propertyPath: 'age_maximum')]
    private ?float $age_minimum = null;

    #[ORM\Column(type: 'decimal', precision: 3, scale: 1, nullable: true)]
    #[Assert\GreaterThan(propertyPath: 'age_minimum')]
    private ?float $age_maximum = null;

    #[ORM\Column(type: 'boolean', length: 10, nullable: false)]
    private ?bool $is_plaine = false;

    /**
     * @var Enfant[]
     */
    #[ORM\OneToMany(targetEntity: Enfant::class, mappedBy: 'groupe_scolaire')]
    private Collection|array $enfants = [];

    /**
     * @var AnneeScolaire[]
     */
    #[ORM\OneToMany(targetEntity: AnneeScolaire::class, mappedBy: 'groupe_scolaire')]
    private Collection|array $annees_scolaires = [];

    /**
     * Pour la cascade.
     *
     * @var PlaineGroupe[]
     */
    #[ORM\OneToMany(targetEntity: PlaineGroupe::class, mappedBy: 'groupe_scolaire', cascade: ['remove'])]
    private Collection|array $plaine_groupes = [];

    public function __construct()
    {
        $this->enfants = new ArrayCollection();
        $this->annees_scolaires = new ArrayCollection();
        $this->plaine_groupes = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string) $this->nom;
    }

    public function getAgeMinimum(): ?float
    {
        return $this->age_minimum;
    }

    public function setAgeMinimum(?float $age_minimum): self
    {
        $this->age_minimum = $age_minimum;

        return $this;
    }

    public function getAgeMaximum(): ?float
    {
        return $this->age_maximum;
    }

    public function setAgeMaximum(?float $age_maximum): self
    {
        $this->age_maximum = $age_maximum;

        return $this;
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
        if (!$this->enfants->contains($enfant)) {
            $this->enfants[] = $enfant;
            $enfant->setGroupeScolaire($this);
        }

        return $this;
    }

    public function removeEnfant(Enfant $enfant): self
    {
        // set the owning side to null (unless already changed)
        if ($this->enfants->removeElement($enfant) && $enfant->getGroupeScolaire() === $this) {
            $enfant->setGroupeScolaire(null);
        }

        return $this;
    }

    /**
     * @return Collection|AnneeScolaire[]
     */
    public function getAnneesScolaires(): Collection
    {
        return $this->annees_scolaires;
    }

    public function addAnneesScolaire(AnneeScolaire $anneesScolaire): self
    {
        if (!$this->annees_scolaires->contains($anneesScolaire)) {
            $this->annees_scolaires[] = $anneesScolaire;
            $anneesScolaire->setGroupeScolaire($this);
        }

        return $this;
    }

    public function removeAnneesScolaire(AnneeScolaire $anneesScolaire): self
    {
        // set the owning side to null (unless already changed)
        if ($this->annees_scolaires->removeElement($anneesScolaire) && $anneesScolaire->getGroupeScolaire() === $this) {
            $anneesScolaire->setGroupeScolaire(null);
        }

        return $this;
    }

    /**
     * @return Collection|PlaineGroupe[]
     */
    public function getPlaineGroupes(): Collection
    {
        return $this->plaine_groupes;
    }

    public function addPlaineGroupe(PlaineGroupe $plaineGroupe): self
    {
        if (!$this->plaine_groupes->contains($plaineGroupe)) {
            $this->plaine_groupes[] = $plaineGroupe;
            $plaineGroupe->setGroupeScolaire($this);
        }

        return $this;
    }

    public function removePlaineGroupe(PlaineGroupe $plaineGroupe): self
    {
        // set the owning side to null (unless already changed)
        if ($this->plaine_groupes->removeElement($plaineGroupe) && $plaineGroupe->getGroupeScolaire() === $this) {
            $plaineGroupe->setGroupeScolaire(null);
        }

        return $this;
    }

    public function getIsPlaine(): ?bool
    {
        return $this->is_plaine;
    }

    public function setIsPlaine(bool $is_plaine): self
    {
        $this->is_plaine = $is_plaine;

        return $this;
    }
}
