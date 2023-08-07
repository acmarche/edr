<?php

namespace AcMarche\Edr\Accueil\Utils;

use AcMarche\Edr\Entity\Presence\Accueil;

class AccueilUtils
{
    /**
     * @param array|Accueil[] $accueils
     * @return array|['ecole'=>Ecole, 'accueils'=>Accueil[]]
     */
    public static function groupByEcole(array $accueils): array
    {
        $data = [];
        foreach ($accueils as $accueil) {
            $ecole = $accueil->getEnfant()?->getEcole();
            $data[$ecole->getId()]['ecole'] = $ecole;
            $data[$ecole->getId()]['accueils'][] = $accueil;
        }

        return $data;
    }
}
