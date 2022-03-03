<?php

namespace AcMarche\Edr\Data;

final class EdrConstantes
{
    public const SEXES = [
        'Masculin' => 'Masculin',
        'Féminin' => 'Féminin',
    ];
    public const ORDRES = [
        '0' => 0,
        1 => 1,
        2 => 2,
        'Suivant' => 3,
    ];

    public const ABSENCE_NON = 0;
    public const ABSENCE_AVEC_CERTIF = 1;
    public const ABSENCE_SANS_CERTIF = -1;

    public static function getListAbsences(): array
    {
        return [
            self::ABSENCE_NON => 'Non',
            self::ABSENCE_AVEC_CERTIF => 'Oui avec certificat',
            self::ABSENCE_SANS_CERTIF => 'Oui sans certificat',
        ];
    }

    public static function getAbsenceTxt($number = false): string
    {
        $absences = self::getListAbsences();

        return $absences[$number] ?? $number;
    }

    public static function getCiviliteBySexe(?string $sexe): string
    {
        return match ($sexe) {
            'Masculin' => 'Monsieur',
            'Féminin' => 'Madame',
            default => '',
        };
    }
}
