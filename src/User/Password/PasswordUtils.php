<?php

namespace AcMarche\Edr\User\Password;

use Symfony\Component\String\ByteString;

final class PasswordUtils
{
    public function generatePassword(): ByteString
    {
        return ByteString::fromRandom(6, '0123456789');
    }
}
