<?php

namespace AcMarche\Edr\Entity\Facture;

use AcMarche\Edr\Entity\Plaine\Plaine;
use AcMarche\Edr\Entity\Plaine\PlaineTrait;
use AcMarche\Edr\Entity\Scolaire\Ecole;
use AcMarche\Edr\Entity\Security\Traits\UserAddTrait;
use AcMarche\Edr\Entity\Traits\AdresseTrait;
use AcMarche\Edr\Entity\Traits\CommunicationTrait;
use AcMarche\Edr\Entity\Traits\IdTrait;
use AcMarche\Edr\Entity\Traits\NomTrait;
use AcMarche\Edr\Entity\Traits\PrenomTrait;
use AcMarche\Edr\Entity\Traits\RemarqueTrait;
use AcMarche\Edr\Entity\Traits\TuteurTrait;
use AcMarche\Edr\Entity\Tuteur;
use AcMarche\Edr\Facture\Dto\FactureDetailDto;
use AcMarche\Edr\Facture\FactureInterface;
use AcMarche\Edr\Facture\Repository\FactureRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\UuidableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Knp\DoctrineBehaviors\Model\Uuidable\UuidableTrait;
use Stringable;

#[ORM\Entity(repositoryClass: FactureRepository::class)]
class Facture implements TimestampableInterface, UuidableInterface, FactureInterface, Stringable
{
    use IdTrait;
    use NomTrait;
    use PrenomTrait;
    use TuteurTrait;
    use AdresseTrait;
    use TimestampableTrait;
    use RemarqueTrait;
    use UuidableTrait;
    use FacturePresencesTrait;
    use FactureReductionsTrait;
    use FactureComplementsTrait;
    use FactureDecomptesTrait;
    use UserAddTrait;
    use CommunicationTrait;
    use PlaineTrait;

    #[ORM\ManyToOne(targetEntity: Tuteur::class, inversedBy: 'factures')]
    private ?Tuteur $tuteur = null;

    /**
     * Use for commu factory.
     *
     * @var array|Ecole[]
     */
    public array $ecolesListing = [];

    public ?FactureDetailDto $factureDetailDto = null;

    #[ORM\ManyToOne(targetEntity: Plaine::class, inversedBy: 'factures')]
    private ?Plaine $plaine = null;

    #[ORM\Column(type: 'datetime', nullable: false)]
    private ?DateTimeInterface $factureLe = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTimeInterface $payeLe = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTimeInterface $envoyeLe = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $envoyeA = null;

    #[ORM\Column(type: 'string', length: 100, nullable: false)]
    private ?string $mois = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $plaine_nom = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $ecoles = null;

    #[ORM\Column(type: 'decimal', precision: 6, scale: 2, nullable: true)]
    private float $montant_obsolete;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private float $cloture_obsolete;

    public function __construct(
          Tuteur $tuteur
    ) {
        $this->tuteur = $tuteur;
        $this->facturePresences = new ArrayCollection();
        $this->factureReductions = new ArrayCollection();
        $this->factureComplements = new ArrayCollection();
        $this->factureDecomptes = new ArrayCollection();
    }

    public function __toString(): string
    {
        return 'Facture '.$this->id;
    }

    public function getEnfants(): array
    {
        $enfants = [];
        foreach ($this->facturePresences as $presence) {
            $nom = $presence->getNom().' '.$presence->getPrenom();
            $enfants[$nom] = $nom;
        }

        return $enfants;
    }

    public function getPayeLe(): ?DateTimeInterface
    {
        return $this->payeLe;
    }

    public function setPayeLe(?DateTimeInterface $payeLe): self
    {
        $this->payeLe = $payeLe;

        return $this;
    }

    public function getFactureLe(): ?DateTimeInterface
    {
        return $this->factureLe;
    }

    public function setFactureLe(DateTimeInterface $factureLe): self
    {
        $this->factureLe = $factureLe;

        return $this;
    }

    public function getEnvoyeLe(): ?DateTimeInterface
    {
        return $this->envoyeLe;
    }

    public function setEnvoyeLe(?DateTimeInterface $envoyeLe): self
    {
        $this->envoyeLe = $envoyeLe;

        return $this;
    }

    public function getEnvoyeA(): ?string
    {
        return $this->envoyeA;
    }

    public function setEnvoyeA(?string $envoyeA): self
    {
        $this->envoyeA = $envoyeA;

        return $this;
    }

    public function getMois(): ?string
    {
        return $this->mois;
    }

    public function setMois(string $mois): self
    {
        $this->mois = $mois;

        return $this;
    }

    public function getPlaineNom(): ?string
    {
        return $this->plaine_nom;
    }

    public function setPlaineNom(?string $plaine_nom): self
    {
        $this->plaine_nom = $plaine_nom;

        return $this;
    }

    public function getEcoles(): ?string
    {
        return $this->ecoles;
    }

    public function setEcoles(?string $ecoles): self
    {
        $this->ecoles = $ecoles;

        return $this;
    }

    public function getMontantObsolete(): ?string
    {
        return $this->montant_obsolete;
    }

    public function setMontantObsolete(?string $montant_obsolete): self
    {
        $this->montant_obsolete = $montant_obsolete;

        return $this;
    }

    public function getClotureObsolete(): ?bool
    {
        return $this->cloture_obsolete;
    }

    public function setClotureObsolete(?bool $cloture_obsolete): self
    {
        $this->cloture_obsolete = $cloture_obsolete;

        return $this;
    }
}
