<?php

namespace AcMarche\Edr\Security\Role;

final class EdrSecurityRole
{
    public const ROLE_ADMIN = 'ROLE_MERCREDI_ADMIN';

    public const ROLE_PARENT = 'ROLE_MERCREDI_PARENT';

    public const ROLE_ECOLE = 'ROLE_MERCREDI_ECOLE';

    public const ROLE_ANIMATEUR = 'ROLE_MERCREDI_ANIMATEUR';

    public const ROLES = [
        self::ROLE_ADMIN => 'Administrateur',
        self::ROLE_PARENT => 'Parent',
        self::ROLE_ECOLE => 'Ecole',
        self::ROLE_ANIMATEUR => 'Animateur',
    ];

    public static function niceName(array $roles): array
    {
        $nices = [];
        foreach ($roles as $role) {
            if (isset(self::ROLES[$role])) {
                $nice = self::ROLES[$role];
                $nices[] = $nice;
            }
        }

        return $nices;
    }
}
