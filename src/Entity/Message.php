<?php

namespace AcMarche\Edr\Entity;

use AcMarche\Edr\Entity\Traits\IdTrait;
use AcMarche\Edr\Message\Repository\MessageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'message')]
#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message implements TimestampableInterface
{
    use IdTrait;
    use TimestampableTrait;
    public bool $attachCourriers;

    #[Assert\NotBlank]
    private ?string $from = null;
    /**
     * Assert\NotBlank().
     */
    private ?string $to = null;
    #[ORM\Column(type: 'text', nullable: false)]
    #[Assert\NotBlank]
    private ?string $sujet = null;
    #[ORM\Column(type: 'text', nullable: false)]
    #[Assert\NotBlank]
    private ?string $texte = null;
    private ?UploadedFile $file = null;

    #[ORM\Column(type: 'array', nullable: false)]
    private Collection $destinataires;

    public function __construct()
    {
        $this->destinataires = new ArrayCollection();
    }

    public function getFrom(): ?string
    {
        return $this->from;
    }

    public function setFrom(?string $from): void
    {
        $this->from = $from;
    }

    public function getTo(): ?string
    {
        return $this->to;
    }

    public function setTo(?string $to): void
    {
        $this->to = $to;
    }

    public function getSujet(): ?string
    {
        return $this->sujet;
    }

    public function setSujet(string $sujet): self
    {
        $this->sujet = $sujet;

        return $this;
    }

    public function getTexte(): ?string
    {
        return $this->texte;
    }

    public function setTexte(string $texte): self
    {
        $this->texte = $texte;

        return $this;
    }

    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }

    public function setFile(?UploadedFile $file): void
    {
        $this->file = $file;
    }

    public function getDestinataires(): Collection
    {
        return $this->destinataires;
    }

    public function setDestinataires(array $destinataires): self
    {
        $this->destinataires = new ArrayCollection($destinataires);

        return $this;
    }
}
