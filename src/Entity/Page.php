<?php

namespace AcMarche\Edr\Entity;

use AcMarche\Edr\Entity\Traits\ContentTrait;
use AcMarche\Edr\Entity\Traits\DocumentsTraits;
use AcMarche\Edr\Entity\Traits\IdTrait;
use AcMarche\Edr\Entity\Traits\NomTrait;
use AcMarche\Edr\Page\Repository\PageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Stringable;

#[ORM\Entity(repositoryClass: PageRepository::class)]
class Page implements SluggableInterface, Stringable
{
    use IdTrait;
    use NomTrait;
    use ContentTrait;
    use SluggableTrait;
    use DocumentsTraits;
    public bool $system;
    #[ORM\Column(type: 'text', length: 100, nullable: true)]
    private ?string $slug_system = null;
    #[ORM\Column(type: 'smallint', nullable: true)]
    private ?int $position = null;

    #[ORM\Column(type: 'boolean')]
    private bool $menu;

    /**
     * @var Document[]
     */
    #[ORM\ManyToMany(targetEntity: Document::class)]
    private Collection $documents;

    public function __construct()
    {
        $this->system = false;
        $this->menu = true;
        $this->documents = new ArrayCollection();
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

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getSlugSystem(): ?string
    {
        return $this->slug_system;
    }

    public function setSlugSystem(?string $slug_system): self
    {
        $this->slug_system = $slug_system;

        return $this;
    }

    public function isMenu(): bool
    {
        return $this->menu;
    }

    public function setMenu(bool $menu): void
    {
        $this->menu = $menu;
    }

}
