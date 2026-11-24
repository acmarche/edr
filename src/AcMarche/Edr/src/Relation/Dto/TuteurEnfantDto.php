<?php

namespace AcMarche\Edr\Relation\Dto;

use AcMarche\Edr\Entity\Traits\EnfantTrait;
use AcMarche\Edr\Entity\Traits\TuteurTrait;

final class TuteurEnfantDto
{
    use EnfantTrait;
    use TuteurTrait;
}
