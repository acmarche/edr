<?php

namespace AcMarche\Edr\Facture\Validator;

use AcMarche\Edr\Entity\Facture\FactureReduction;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PourcentageOrForfaitValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        /** @var FactureReduction $constraint */

        if ($value->getPourcentage() && $value->getForfait()) {
            $this->context->buildViolation($constraint->message_only_one)
                ->atPath('facture_reduction[pourcentage]')
                ->addViolation();
        }

        if (!$value->getPourcentage() && !$value->getForfait()) {
            $this->context->buildViolation($constraint->message_only_one)
                ->atPath('facture_reduction[pourcentage]')
                ->addViolation();
        }
    }
}
