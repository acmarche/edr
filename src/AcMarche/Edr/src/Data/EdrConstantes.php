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

    public static function getCiviliteBySexe(?string $sexe): string
    {
        return match ($sexe) {
            'Masculin' => 'Monsieur',
            'Féminin' => 'Madame',
            default => '',
        };
    }
}
