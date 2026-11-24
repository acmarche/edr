<?php

namespace AcMarche\Edr\ServiceIterator;

interface AfterUserRegistration
{
    public function afterUserRegistrationSuccessful(): void;
}
