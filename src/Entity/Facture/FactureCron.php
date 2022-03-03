<?php

namespace AcMarche\Edr\Entity\Facture;

use AcMarche\Edr\Entity\Security\Traits\UserAddTrait;
use AcMarche\Edr\Entity\Traits\IdTrait;
use AcMarche\Edr\Facture\Repository\FactureCronRepository;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;

#[ORM\Entity(repositoryClass: FactureCronRepository::class)]
class FactureCron implements TimestampableInterface
{
    use IdTrait;
    use UserAddTrait;
    use TimestampableTrait;
    #[ORM\Column(type: 'boolean', nullable: false)]
    private bool $done = false;

    public function __construct(
        #[ORM\Column(type: 'string', length: 50, nullable: false)] private string $fromAdresse,
        #[ORM\Column(type: 'string', length: 150, nullable: false)] private string $subject,
        #[ORM\Column(type: 'text', nullable: false)] private string $body,
        #[ORM\Column(type: 'string', length: 50, unique: true, nullable: false)] private string $month
    ) {
    }

    public function getMonth(): string
    {
        return $this->month;
    }

    public function setMonth(string $month): self
    {
        $this->month = $month;

        return $this;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getFromAdresse(): string
    {
        return $this->fromAdresse;
    }

    public function setFromAdresse(string $fromAdresse): self
    {
        $this->fromAdresse = $fromAdresse;

        return $this;
    }

    public function getDone(): bool
    {
        return $this->done;
    }

    public function setDone(bool $done): self
    {
        $this->done = $done;

        return $this;
    }
}
