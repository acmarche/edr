<?php

namespace AcMarche\Edr\Entity\Traits;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

trait PhotoTrait
{
    #[Vich\UploadableField(mapping: 'edr_enfant_image', fileNameProperty: 'photoName')]
    #[Assert\Image(maxSize: '7M')]
    private ?File $photo = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $photoName = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $mime = null;

    public function setPhoto(File|UploadedFile $file = null): void
    {
        $this->photo = $file;

        if ($file instanceof File) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime('now');
        }
    }

    public function getPhoto(): ?File
    {
        return $this->photo;
    }

    public function getPhotoName(): ?string
    {
        return $this->photoName;
    }

    public function setPhotoName(?string $photoName): void
    {
        $this->photoName = $photoName;
    }

    public function getMime(): ?string
    {
        return $this->mime;
    }

    public function setMime(?string $mime): void
    {
        $this->mime = $mime;
    }
}
