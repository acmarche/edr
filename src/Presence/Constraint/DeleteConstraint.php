<?php

namespace AcMarche\Edr\Presence\Constraint;

use AcMarche\Edr\Entity\Presence\Accueil;
use AcMarche\Edr\Entity\Presence\Presence;
use DateTime;
use DateTimeInterface;

/**
 * Pour les parents ne peuvent supprimer une date passée
 * Class DeleteConstraint.
 */
final class DeleteConstraint
{
    public static function canBeDeleted(Presence $presence): bool
    {
        $dateTime = new DateTime();

        return $presence->getJour()->getDateJour() > $dateTime;
    }

    public static function accueilCanBeDeleted(Accueil $accueil): bool
    {
        $dateTime = new DateTime();

        return $accueil->getDateJour() > $dateTime;
    }

    public function constraintDelete(DateTimeInterface $dateTime, $today = null): bool
    {
        /*
         * Si on est un mardi la veille !
         * alors il faut qu'on soit max mardi 12h00
         * si on reserve un mardi 6 pour un admin 7
         */
        if (2 === $today->format('N')) {
            $lendemain = clone $today;
            $lendemain = $lendemain->modify('+1 day');
            //la veille ?
            if ($lendemain->format('d-m-Y') === $dateTime->format('d-m-Y')) {
                //si après 10h
                $heure = (int) $today->format('G');
                $minute = (int) $today->format('i');
                if ($heure > 10) {
                    return false;
                }

                //si après 10h02
                if (10 === $heure && $minute > 02) {
                    return false;
                }
            }
        }

        return true;
    }
}
