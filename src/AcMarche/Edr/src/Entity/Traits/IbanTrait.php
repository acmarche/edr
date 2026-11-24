<?php

namespace AcMarche\Edr\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait IbanTrait
{
    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    #[Assert\Iban(message: 'This is not a valid International Bank Account Number (IBAN).')]
    protected ?string $iban = null;

    public function getIban(): ?string
    {
        return $this->iban;
    }

    public function setIban(?string $iban): void
    {
        $this->iban = $iban;
    }
}
