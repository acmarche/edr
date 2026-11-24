<?php

namespace AcMarche\Edr\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait IdOldTrait
{
    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $idOld = null;

    public function getIdOld(): ?int
    {
        return $this->idOld;
    }

    public function setIdOld(?int $idOld): void
    {
        $this->idOld = $idOld;
    }
}
