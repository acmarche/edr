<?php

namespace AcMarche\Edr\Facture\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class PourcentageOrForfait extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public string $message_only_one = 'Vous devez appliquer un pourcentage ou une réduction';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
