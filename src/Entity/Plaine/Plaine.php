<?php

namespace AcMarche\Edr\Entity\Plaine;

use AcMarche\Edr\Entity\Facture\Facture;
use AcMarche\Edr\Entity\Facture\FacturesTrait;
use AcMarche\Edr\Entity\Jour;
use AcMarche\Edr\Entity\Traits\ArchiveTrait;
use AcMarche\Edr\Entity\Traits\CommunicationTrait;
use AcMarche\Edr\Entity\Traits\IdOldTrait;
use AcMarche\Edr\Entity\Traits\IdTrait;
use AcMarche\Edr\Entity\Traits\NomTrait;
use AcMarche\Edr\Entity\Traits\PrixTrait;
use AcMarche\Edr\Entity\Traits\RemarqueTrait;
use AcMarche\Edr\Plaine\Repository\PlaineRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Stringable;

#[ORM\Entity(repositoryClass: PlaineRepository::class)]
class Plaine implements SluggableInterface, Stringable
{
    use IdTrait;
    use NomTrait;
    use RemarqueTrait;
    use InscriptionOpenTrait;
    use PrixTrait;
    use PrematernelleTrait;
    use PlaineGroupesTrait;
    use SluggableTrait;
    use ArchiveTrait;
    use JoursTrait;
    use CommunicationTrait;
    use FacturesTrait;
    use IdOldTrait;
    public array $enfants = [];
    /**
     * @var Jour[]
     */
    #[ORM\OneToMany(targetEntity: Jour::class, mappedBy: 'plaine', cascade: ['remove'])]
    private Collection $jours;
    /**
     * @var PlaineGroupe[]|null
     */
    #[ORM\OneToMany(targetEntity: PlaineGroupe::class, mappedBy: 'plaine', cascade: ['remove', 'persist'])]
    private Collection $plaine_groupes;
    #[ORM\Column(type: 'string', length: 100, nullable: true, unique: false)]
    private ?string $communication = null;
    /**
     * @var Facture[]
     */
    #[ORM\OneToMany(targetEntity: Facture::class, mappedBy: 'plaine', cascade: ['remove'])]
    private Collection $factures;

    public function __construct()
    {
        $this->jours = new ArrayCollection();
        $this->plaine_groupes = new ArrayCollection();
        $this->inscriptionOpen = false;
        $this->prix1 = 0;
        $this->prix2 = 0;
        $this->prix3 = 0;
    }

    public function __toString(): string
    {
        return $this->nom;
    }

    public function getSluggableFields(): array
    {
        return ['nom'];
    }

    public function shouldGenerateUniqueSlugs(): bool
    {
        return true;
    }

    public function getFirstDay(): Jour
    {
        return $this->jours[0];
    }
}
