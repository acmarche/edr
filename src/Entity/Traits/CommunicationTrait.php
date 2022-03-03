<?php

namespace AcMarche\Edr\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait CommunicationTrait
{
    #[ORM\Column(type: 'string', length: 100, nullable: true, unique: true)]
    private ?string $communication = null;

    public function getCommunication(): ?string
    {
        return $this->communication;
    }

    public function setCommunication(string $communication): self
    {
        $this->communication = $communication;

        return $this;
    }
}
