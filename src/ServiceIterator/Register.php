<?php

namespace AcMarche\Edr\ServiceIterator;

final readonly class Register
{
    public function __construct(
        private iterable $secondaryFlows
    ) {
    }

    public function exe(): void
    {
        foreach ($this->secondaryFlows as $flow) {
            $flow->afterUserRegistrationSuccessful();
        }
    }
}
