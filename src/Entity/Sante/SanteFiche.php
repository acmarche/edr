<?php

namespace AcMarche\Edr\Entity\Sante;

use AcMarche\Edr\Entity\Enfant;
use AcMarche\Edr\Entity\Traits\AccompagnateursTrait;
use AcMarche\Edr\Entity\Traits\EnfantTrait;
use AcMarche\Edr\Entity\Traits\IdOldTrait;
use AcMarche\Edr\Entity\Traits\IdTrait;
use AcMarche\Edr\Entity\Traits\RemarqueTrait;
use AcMarche\Edr\Sante\Repository\SanteFicheRepository;
use AcMarche\Edr\Sante\Validator as AcMarcheSanteAssert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Stringable;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SanteFicheRepository::class)]
#[ORM\Table(name: 'sante_fiche')]
#[ORM\UniqueConstraint(columns: ['enfant_id'])]
class SanteFiche implements TimestampableInterface, Stringable
{
    use TimestampableTrait;
    use IdTrait;
    use EnfantTrait;
    use AccompagnateursTrait;
    use RemarqueTrait;
    use IdOldTrait;

    #[ORM\OneToOne(targetEntity: Enfant::class, inversedBy: 'sante_fiche')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Enfant $enfant = null;

    #[ORM\Column(type: 'text', nullable: false)]
    #[Assert\NotBlank]
    private ?string $personne_urgence = null;
    #[ORM\Column(type: 'string', length: 200, nullable: false)]
    #[Assert\NotBlank]
    private ?string $medecin_nom = null;
    #[ORM\Column(type: 'string', length: 200, nullable: false)]
    #[Assert\NotBlank]
    private ?string $medecin_telephone = null;
    /**
     * Pour le cascade.
     *
     * @var SanteReponse[]
     */
    #[ORM\OneToMany(targetEntity: SanteReponse::class, mappedBy: 'sante_fiche', cascade: ['remove'])]
    private Collection $reponses;
    /**
     * @var SanteQuestion[]|Collection
     * @AcMarcheSanteAssert\ResponseIsComplete()
     */
    private Collection $questions;

    public function __construct(
        Enfant $enfant
    ) {
        $this->reponses = new ArrayCollection();
        $this->questions = new ArrayCollection();
        $this->enfant = $enfant;
    }

    public function __toString(): string
    {
        return 'Fiche '.$this->id;
    }

    public function getPersonneUrgence(): ?string
    {
        return $this->personne_urgence;
    }

    public function setPersonneUrgence(string $personne_urgence): self
    {
        $this->personne_urgence = $personne_urgence;

        return $this;
    }

    public function getMedecinNom(): ?string
    {
        return $this->medecin_nom;
    }

    public function setMedecinNom(string $medecin_nom): self
    {
        $this->medecin_nom = $medecin_nom;

        return $this;
    }

    public function getMedecinTelephone(): ?string
    {
        return $this->medecin_telephone;
    }

    public function setMedecinTelephone(string $medecin_telephone): self
    {
        $this->medecin_telephone = $medecin_telephone;

        return $this;
    }

    /**
     * @return Collection|SanteReponse[]
     */
    public function getReponses(): Collection
    {
        return $this->reponses;
    }

    /**
     * @param SanteReponse[] $reponses
     */
    public function setReponses(array $reponses): void
    {
        $this->reponses = new ArrayCollection($reponses);
    }

    /**
     * @return SanteQuestion[]|Collection
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    /**
     * @param SanteQuestion[]|Collection $questions
     */
    public function setQuestions(array|Collection $questions): void
    {
        $this->questions = new ArrayCollection($questions);
    }

    public function addReponse(SanteReponse $reponse): self
    {
        if (!$this->reponses->contains($reponse)) {
            $this->reponses[] = $reponse;
            $reponse->setSanteFiche($this);
        }

        return $this;
    }

    public function removeReponse(SanteReponse $reponse): self
    {
        // set the owning side to null (unless already changed)
        if ($this->reponses->removeElement($reponse) && $reponse->getSanteFiche() === $this) {
            $reponse->setSanteFiche(null);
        }

        return $this;
    }
}
