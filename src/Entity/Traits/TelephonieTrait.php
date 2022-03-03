<?php

namespace AcMarche\Edr\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait TelephonieTrait
{
    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 150, nullable: true)]
    private ?string $telephone = null;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 150, nullable: true)]
    private ?string $telephone_bureau = null;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 150, nullable: true)]
    private ?string $gsm = null;

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): void
    {
        $this->telephone = $telephone;
    }

    public function getTelephoneBureau(): ?string
    {
        return $this->telephone_bureau;
    }

    public function setTelephoneBureau(?string $telephone_bureau): void
    {
        $this->telephone_bureau = $telephone_bureau;
    }

    public function getGsm(): ?string
    {
        return $this->gsm;
    }

    public function setGsm(?string $gsm): void
    {
        $this->gsm = $gsm;
    }
}
