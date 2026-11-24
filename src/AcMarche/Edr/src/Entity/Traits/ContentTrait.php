<?php

namespace AcMarche\Edr\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait ContentTrait
{
    /**
     * @var string|null
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $content = null;

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): void
    {
        $this->content = $content;
    }
}
