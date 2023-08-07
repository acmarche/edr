<?php

namespace AcMarche\Edr\Entity;

use AcMarche\Edr\Document\Repository\DocumentRepository;
use AcMarche\Edr\Entity\Traits\FileTrait;
use AcMarche\Edr\Entity\Traits\IdTrait;
use AcMarche\Edr\Entity\Traits\NomTrait;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\UuidableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Knp\DoctrineBehaviors\Model\Uuidable\UuidableTrait;
use Stringable;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[Vich\Uploadable]
#[ORM\Entity(repositoryClass: DocumentRepository::class)]
class Document implements TimestampableInterface, UuidableInterface, Stringable
{
    use IdTrait;
    use UuidableTrait;
    use TimestampableTrait;
    use NomTrait;
    use FileTrait;

    #[Vich\UploadableField(mapping: 'edr_document', fileNameProperty: 'fileName', mimeType: "mimeType", size: "fileSize")]
    #[Assert\File(maxSize: '10M', mimeTypes: ['application/pdf', 'application/x-pdf', 'image/*'], mimeTypesMessage: 'Uniquement des images ou Pdf')]
    private ?File $file = null;

    public function __toString(): string
    {
        return $this->nom;
    }
}
