<?php

namespace AcMarche\Edr\Tuteur\Utils;

use AcMarche\Edr\Entity\Enfant;
use AcMarche\Edr\Entity\Security\User;
use AcMarche\Edr\Entity\Tuteur;
use AcMarche\Edr\Relation\Repository\RelationRepository;
use AcMarche\Edr\Relation\Utils\RelationUtils;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class TuteurUtils
{
    public function __construct(
        private RelationRepository $relationRepository
    ) {
    }

    public static function getTelephones(Tuteur $tuteur): string
    {
        $telephones = '';
        $gsm = $tuteur->getGsm();
        $gsmConjoint = $tuteur->getGsmConjoint();
        $telephoneBureau = $tuteur->getTelephoneBureau();
        $telephoneBureauConjoint = $tuteur->getTelephoneBureauConjoint();
        $telephone = $tuteur->getTelephone();
        $telephoneConjoint = $tuteur->getTelephoneConjoint();

        if ($gsm || $gsmConjoint) {
            $telephones .= $gsm . ' | ' . $gsmConjoint;
        } elseif ($telephoneBureau || $telephoneBureauConjoint) {
            $telephones .= $telephoneBureau . ' | ' . $telephoneBureauConjoint;
        } else {
            $telephones .= $telephone . ' | ' . $telephoneConjoint;
        }

        return $telephones;
    }

    public static function coordonneesIsComplete(Tuteur $tuteur): bool
    {
        if ('' === self::getTelephones($tuteur)) {
            return false;
        }

        if (!$tuteur->getNom()) {
            return false;
        }

        if (!$tuteur->getPrenom()) {
            return false;
        }

        if (!$tuteur->getRue()) {
            return false;
        }

        if (!$tuteur->getCodePostal()) {
            return false;
        }

        return (bool) $tuteur->getLocalite();
    }

    /**
     * Retourne un tableau de string contentant les emails.
     *
     * @param Tuteur[] $tuteurs
     */
    public function getEmails(array $tuteurs): array
    {
        $emails = [[]];
        foreach ($tuteurs as $tuteur) {
            if ($this->tuteurIsActif($tuteur) && ($tmp = self::getEmailsOfOneTuteur($tuteur))) {
                $emails[] = $tmp;
            }
        }

        $emails = array_merge(...$emails);

        return array_unique($emails);
    }

    public function tuteurIsActif(Tuteur $tuteur): bool
    {
        return [] !== $this->relationRepository->findEnfantsActifs($tuteur);
    }

    /**
     * @param UserInterface|User $user
     */
    public function getTuteurByUser(User|UserInterface $user): ?Tuteur
    {
        $tuteurs = $user->getTuteurs();

        if (0 === (is_countable($tuteurs) ? \count($tuteurs) : 0)) {
            return null;
        }

        return $tuteurs[0];
    }

    /**
     * @return string[]
     */
    public static function getEmailsOfOneTuteur(Tuteur $tuteur): array
    {
        $emails = [];

        if (filter_var($tuteur->getEmail(), FILTER_VALIDATE_EMAIL)) {
            $emails[] = $tuteur->getEmail();
        }

        if (\count($tuteur->getUsers()) > 0) {
            $users = $tuteur->getUsers();
            $user = $users[0];
            if (filter_var($user->getEmail(), FILTER_VALIDATE_EMAIL)) {
                $emails[] = $user->getEmail();
            }
        }

        if (filter_var($tuteur->getEmailConjoint(), FILTER_VALIDATE_EMAIL)) {
            $emails[] = $tuteur->getEmailConjoint();
        }

        return array_unique($emails);
    }

    /**
     * Retourne la liste des tuteurs qui n'ont pas d'emails.
     *
     * @param Tuteur[] $tuteurs
     *
     * @return Tuteur[]
     */
    public function filterTuteursWithOutEmail(array $tuteurs): array
    {
        $data = [];
        foreach ($tuteurs as $tuteur) {
            if (!$this->tuteurIsActif($tuteur)) {
                continue;
            }

            $t = self::getEmailsOfOneTuteur($tuteur);
            if (null !== $t) {
                continue;
            }

            $data[] = $tuteur;
        }

        return $data;
    }

    /**
     * @param Enfant[] $enfants
     *
     * @return Tuteur[]
     */
    public function getTuteursByEnfants(array $enfants): array
    {
        $relations = $this->relationRepository->findByEnfants($enfants);

        return RelationUtils::extractTuteurs($relations);
    }
}
