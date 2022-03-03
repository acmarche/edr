<?php

namespace AcMarche\Edr\Entity\Traits;

use AcMarche\Edr\Entity\Presence\Presence;

trait PresenceTrait
{
    /**
     * @var Presence
     */
    private ?Presence $presence = null;

    public function getPresence(): Presence
    {
        return $this->presence;
    }

    public function setPresence(Presence $presence): void
    {
        $this->presence = $presence;
    }
}
