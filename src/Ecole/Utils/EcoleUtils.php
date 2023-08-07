<?php

namespace AcMarche\Edr\Ecole\Utils;

use AcMarche\Edr\Entity\Scolaire\Ecole;
use AcMarche\Edr\Entity\Security\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class EcoleUtils
{
    /**
     * @return Ecole[]|Collection
     */
    public function getEcolesByUser(User $user): iterable
    {
        return $user->getEcoles();
    }

    /**
     * @param Ecole[]|Collection $ecoles
     */
    public static function getNamesEcole(array|Collection $ecoles): string
    {
        $noms = array_map(
            static fn($ecole) => $ecole->getNom(),
            $ecoles->toArray()
        );

        return implode(',', $noms);
    }
}
