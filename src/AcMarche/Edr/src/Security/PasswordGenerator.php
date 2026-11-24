<?php

namespace AcMarche\Edr\Security;

final class PasswordGenerator
{
    public static function generatePassword(): string
    {
        $password = '';

        for ($i = 0; $i < 6; ++$i) {
            $password .= random_int(1, 9);
        }

        return $password;
    }
}
