<?php

namespace AcMarche\Edr\Entity;

use AcMarche\Edr\Entity\Security\Traits\UserAddTrait;
use AcMarche\Edr\Migration\PaiementRepository;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Stringable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @deprecated  for migration
 */
#[ORM\Table(name: 'paiement')]
#[ORM\Entity(repositoryClass: PaiementRepository::class)]
class Paiement implements Stringable
{
    use TimestampableTrait;
    use UserAddTrait;
    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[ORM\Column(type: 'decimal', precision: 6, scale: 2, nullable: false)]
    #[Assert\NotBlank]
    protected $montant;

    #[ORM\Column(type: 'date', nullable: false)]
    protected ?DateTimeInterface $date_paiement = null;

    #[ORM\Column(type: 'string', nullable: true, length: 150)]
    protected ?string $type_paiement = null;

    #[ORM\Column(type: 'string', nullable: true, length: 150)]
    protected ?string $mode_paiement = null;

    #[ORM\Column(type: 'smallint', length: 2, nullable: true, options: [
        'comment' => '1,2, suviant',
        'default' => '0',
    ])]
    protected ?int $ordre = 0;

    #[ORM\Column(type: 'boolean', options: [
        'default' => '0',
    ])]
    protected bool $cloture = false;

    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $remarques = null;

    #[ORM\ManyToOne(targetEntity: 'Tuteur')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    protected ?Tuteur $tuteur = null;

    #[ORM\ManyToOne(targetEntity: 'Enfant')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    protected ?Enfant $enfant = null;

    public function __construct()
    {
    }

    public function __toString(): string
    {
        $string = $this->getTypePaiement().' du ';
        if (null !== $this->getDatePaiement()) {
            $string .= $this->getDatePaiement()->format('d-m-Y');
        }

        return $string.(' ('.$this->getMontant().' €)');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): ?string
    {
        return $this->montant;
    }

    public function setMontant(string $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    /**
     * @return DateTime|DateTimeImmutable
     */
    public function getDatePaiement(): ?DateTimeInterface
    {
        return $this->date_paiement;
    }

    public function setDatePaiement(DateTimeInterface $date_paiement): self
    {
        $this->date_paiement = $date_paiement;

        return $this;
    }

    public function getTypePaiement(): ?string
    {
        return $this->type_paiement;
    }

    public function setTypePaiement(?string $type_paiement): self
    {
        $this->type_paiement = $type_paiement;

        return $this;
    }

    public function getModePaiement(): ?string
    {
        return $this->mode_paiement;
    }

    public function setModePaiement(?string $mode_paiement): self
    {
        $this->mode_paiement = $mode_paiement;

        return $this;
    }

    public function getOrdre(): ?int
    {
        return $this->ordre;
    }

    public function setOrdre(?int $ordre): self
    {
        $this->ordre = $ordre;

        return $this;
    }

    public function getCloture(): bool
    {
        return $this->cloture;
    }

    public function setCloture(bool $cloture): self
    {
        $this->cloture = $cloture;

        return $this;
    }

    public function getRemarques(): ?string
    {
        return $this->remarques;
    }

    public function setRemarques(?string $remarques): self
    {
        $this->remarques = $remarques;

        return $this;
    }

    public function getTuteur(): ?Tuteur
    {
        return $this->tuteur;
    }

    public function setTuteur(?Tuteur $tuteur): self
    {
        $this->tuteur = $tuteur;

        return $this;
    }

    public function getEnfant(): ?Enfant
    {
        return $this->enfant;
    }

    public function setEnfant(?Enfant $enfant): self
    {
        $this->enfant = $enfant;

        return $this;
    }
}
