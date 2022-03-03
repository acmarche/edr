<?php

namespace AcMarche\Edr\Entity\Sante;

use AcMarche\Edr\Entity\Traits\IdOldTrait;
use AcMarche\Edr\Entity\Traits\IdTrait;
use AcMarche\Edr\Sante\Repository\SanteReponseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SanteReponseRepository::class)]
#[ORM\Table(name: 'sante_reponse')]
#[ORM\UniqueConstraint(columns: ['sante_fiche_id', 'question_id'])]
class SanteReponse
{
    use IdTrait;
    use IdOldTrait;
    #[ORM\Column(type: 'boolean')]
    private bool $reponse;
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $remarque = null;

    public function __construct(
        #[ORM\ManyToOne(targetEntity: SanteFiche::class, inversedBy: 'reponses', cascade: ['remove'])] #[ORM\JoinColumn(nullable: false)] private SanteFiche $sante_fiche,
        #[ORM\ManyToOne(targetEntity: SanteQuestion::class, inversedBy: 'reponse')] #[ORM\JoinColumn(nullable: false)] private SanteQuestion $question
    ) {
        $this->reponse = false;
    }

    public function getQuestion(): SanteQuestion
    {
        return $this->question;
    }

    public function setQuestion(?SanteQuestion $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function getSanteFiche(): SanteFiche
    {
        return $this->sante_fiche;
    }

    public function setSanteFiche(?SanteFiche $sante_fiche): self
    {
        $this->sante_fiche = $sante_fiche;

        return $this;
    }

    public function isReponse(): bool
    {
        return $this->reponse;
    }

    public function setReponse(bool $reponse): self
    {
        $this->reponse = $reponse;

        return $this;
    }

    public function getRemarque(): ?string
    {
        return $this->remarque;
    }

    public function setRemarque(?string $remarque): self
    {
        $this->remarque = $remarque;

        return $this;
    }

    public function getReponse(): bool
    {
        return $this->reponse;
    }
}
