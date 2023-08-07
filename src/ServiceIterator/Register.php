<?php

namespace AcMarche\Edr\ServiceIterator;

final class Register
{
    public function __construct(
        private readonly iterable $secondaryFlows
    ) {
    }

    public function exe(): void
    {
        foreach ($this->secondaryFlows as $flow) {
            $flow->afterUserRegistrationSuccessful();
        }
    }
}
