<?php

namespace AcMarche\Edr\Accueil\Contrat;

interface AccueilInterface
{
    public const MATIN = 'Matin';
    public const SOIR = 'Soir';
    public const HEURES = [
        self::MATIN => 'Au matin',
        self::SOIR => 'Au soir',
    ];
}
