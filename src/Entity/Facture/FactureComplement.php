<?php

namespace AcMarche\Edr\Entity\Facture;

use AcMarche\Edr\Entity\Traits\IdTrait;
use AcMarche\Edr\Entity\Traits\NomTrait;
use AcMarche\Edr\Facture\FactureInterface;
use AcMarche\Edr\Facture\Repository\FactureComplementRepository;
use AcMarche\Edr\Facture\Validator as AcMarcheValidator;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\UuidableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Knp\DoctrineBehaviors\Model\Uuidable\UuidableTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @AcMarcheValidator\PourcentageOrForfait()
 */
#[ORM\Entity(repositoryClass: FactureComplementRepository::class)]
#[ORM\Table(name: 'facture_complement')]
class FactureComplement implements TimestampableInterface, UuidableInterface
{
    use IdTrait;
    use NomTrait;
    use FactureTrait;
    use UuidableTrait;
    use TimestampableTrait;

    #[ORM\ManyToOne(targetEntity: Facture::class, inversedBy: 'factureComplements')]
    private FactureInterface $facture;
    #[ORM\Column(type: 'decimal', precision: 4, scale: 2, nullable: true)]
    #[Assert\Range(min: 0)]
    private ?float $forfait = null;
    #[ORM\Column(type: 'decimal', precision: 4, scale: 2, nullable: true)]
    #[Assert\Range(min: 0, max: 100)]
    private ?float $pourcentage = null;
    #[ORM\Column(type: 'datetime', nullable: false)]
    private ?DateTimeInterface $dateLe = null;

    public function __construct(
        Facture $facture
    ) {
        $this->facture = $facture;
    }

    public function getForfait(): ?float
    {
        return $this->forfait;
    }

    public function setForfait(?float $forfait): self
    {
        $this->forfait = $forfait;

        return $this;
    }

    public function getPourcentage(): ?float
    {
        return $this->pourcentage;
    }

    public function setPourcentage(?float $pourcentage): self
    {
        $this->pourcentage = $pourcentage;

        return $this;
    }

    public function getDateLe(): ?\DateTimeInterface
    {
        return $this->dateLe;
    }

    public function setDateLe(\DateTimeInterface $dateLe): self
    {
        $this->dateLe = $dateLe;

        return $this;
    }
}
