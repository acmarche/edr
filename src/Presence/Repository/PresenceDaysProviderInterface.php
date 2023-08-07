<?php

namespace AcMarche\Edr\Presence\Repository;

use AcMarche\Edr\Entity\Enfant;
use AcMarche\Edr\Entity\Jour;

interface PresenceDaysProviderInterface
{
    /**
     * @return Jour[]
     */
    public function getAllDaysToSubscribe(Enfant $enfant): array
    ;
}
