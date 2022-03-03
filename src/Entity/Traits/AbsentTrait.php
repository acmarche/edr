<?php

namespace AcMarche\Edr\Entity\Traits;

use AcMarche\Edr\Data\EdrConstantes;
use Doctrine\ORM\Mapping as ORM;

trait AbsentTrait
{
    /**
     * @see EdrConstantes::ABSENCE_AVEC_CERTIF
     */
    #[ORM\Column(type: 'smallint', length: 2, nullable: false, options: [
        'comment' => '-1 sans certif, 1 avec certfi',
    ])]
    private int $absent = 0;

    public function getAbsent(): int
    {
        return $this->absent;
    }

    public function setAbsent(int $absent): void
    {
        $this->absent = $absent;
    }
}
