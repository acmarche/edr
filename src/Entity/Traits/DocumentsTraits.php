<?php

namespace AcMarche\Edr\Entity\Traits;

use AcMarche\Edr\Entity\Document;
use Doctrine\Common\Collections\Collection;

trait DocumentsTraits
{
    /**
     * @var Document[]|Collection
     */
    private Collection $documents;

    /**
     * @return Collection|Document[]
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Document $document): self
    {
        if (! $this->documents->contains($document)) {
            $this->documents[] = $document;
        }

        return $this;
    }

    public function removeDocument(Document $document): self
    {
        if ($this->documents->contains($document)) {
            $this->documents->removeElement($document);
        }

        return $this;
    }
}
