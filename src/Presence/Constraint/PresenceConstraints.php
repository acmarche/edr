<?php

namespace AcMarche\Edr\Presence\Constraint;

use AcMarche\Edr\Contrat\Presence\PresenceConstraintInterface;

final class PresenceConstraints
{
    /**
     * @var iterable|PresenceConstraintInterface
     */
    private iterable $constraints = [];

    /**
     * @var PresenceConstraintInterface[]
     */
    private array $constraints2 = [];

    public function __construct()
    {
    }

    //todo try it
    //https://woutercarabain.com/webdevelopment/how-to-inject-multiple-instances-of-an-interface-in-a-service-using-symfony-5/
    public function construct(array $providers = []): void
    {
        $this->constraints2 = $providers;
    }

    public function addConstraint(PresenceConstraintInterface $constraint): void
    {
        $this->constraints[] = $constraint;
    }

    public function execute($jour): void
    {
        foreach ($this->constraints as $constraint) {
            $constraint->check($jour);
        }
    }

    public function getConstraints(): array
    {
        return $this->constraints;
    }
}
