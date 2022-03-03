<?php

namespace AcMarche\Edr\Accueil\Calculator;

use AcMarche\Edr\Entity\Presence\Accueil;

interface AccueilCalculatorInterface
{
    public function getPrix(Accueil $accueil): float;

    public function calculate(Accueil $accueil): float;

    public function calculateRetard(Accueil $accueil): float;
}
