<?php

namespace AcMarche\Edr\Entity\Traits;

use AcMarche\Edr\Entity\Note;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

trait EnfantNotesTrait
{
    /**
     * @var Collection|Note[]
     */
    #[ORM\OneToMany(targetEntity: Note::class, mappedBy: 'enfant', cascade: ['remove'])]
    private Collection $notes;

    /**
     * @return Collection|Note[]
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Note $note): self
    {
        if (! $this->notes->contains($note)) {
            $this->notes[] = $note;
        }

        return $this;
    }

    public function removeNote(Note $note): self
    {
        if ($this->notes->contains($note)) {
            $this->notes->removeElement($note);
        }

        return $this;
    }
}
