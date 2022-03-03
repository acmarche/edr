<?php

namespace AcMarche\Edr\Presence\Utils;

use AcMarche\Edr\Entity\Enfant;
use AcMarche\Edr\Entity\Jour;
use AcMarche\Edr\Entity\Plaine\Plaine;
use AcMarche\Edr\Entity\Presence\Presence;
use AcMarche\Edr\Entity\Tuteur;
use AcMarche\Edr\Parameter\Option;
use AcMarche\Edr\Relation\Repository\RelationRepository;
use AcMarche\Edr\Tuteur\Utils\TuteurUtils;
use AcMarche\Edr\Utils\SortUtils;
use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

final class PresenceUtils
{
    public function __construct(
        private ParameterBagInterface $parameterBag,
        private RelationRepository $relationRepository
    ) {
    }

    public function getDeadLineDatePresence(): Carbon
    {
        $today = Carbon::today();
        $today->addDays($this->parameterBag->get(Option::PRESENCE_DEADLINE_DAYS));

        return $today;
    }

    public function getDeadLineDatePedagogique(): Carbon
    {
        $today = Carbon::today();
        $today->addDays($this->parameterBag->get(Option::PEDAGOGIQUE_DEADLINE_DAYS));

        return $today;
    }

    /**
     * @param Presence[] $presences
     */
    public function groupByYear(array $presences): array
    {
        $all = [];
        foreach ($presences as $presence) {
            $jour = $presence->getJour()->getDateJour();
            $all[$jour->format('Y')][$jour->format('m')][] = $presence;
        }

        return $all;
    }

    /**
     * @param Presence[] $presences
     *
     * @return Tuteur[]
     */
    public static function extractTuteurs(array $presences): array
    {
        $tuteurs = array_map(
            fn ($presence) => $presence->getTuteur(),
            $presences
        );
        $data = [];
        $tuteurs = SortUtils::sortByName($tuteurs);
        foreach ($tuteurs as $tuteur) {
            $data[$tuteur->getId()] = $tuteur;
        }

        return $data;
    }

    /**
     * @param Presence[] $presences
     *
     * @return Enfant[]
     */
    public static function extractEnfants(array $presences, bool $registerRemarques = false): array
    {
        $enfants =
            array_map(
                function ($presence) use ($registerRemarques) {
                    $enfant = $presence->getEnfant();
                    if ($registerRemarques) {
                        $remarques = $enfant->getRemarque();
                        if ($presence->getRemarque()) {
                            $remarques .= ' (Parent=>) '.$presence->getRemarque();
                        }
                        $enfant->setRemarque($remarques);
                    }

                    return $enfant;
                },
                $presences
            );

        $data = [];
        $enfants = SortUtils::sortByName($enfants);
        foreach ($enfants as $enfant) {
            $data[$enfant->getId()] = $enfant;
        }

        return $data;
    }

    /**
     * @param Presence[] $presences
     *
     * @return Jour[]
     */
    public static function extractJours(array $presences): array
    {
        $jours =
            array_map(
                fn ($presence) => $presence->getJour(),
                $presences
            );
        $data = [];
        foreach ($jours as $jour) {
            $data[$jour->getId()] = $jour;
        }

        return $data;
    }

    /**
     * @param Enfant[] $enfants
     */
    public function addTelephonesOnEnfants(array $enfants): void
    {
        foreach ($enfants as $enfant) {
            $telephones = '';
            foreach ($this->relationRepository->findByEnfant($enfant) as $relation) {
                $tuteur = $relation->getTuteur();
                $telephones .= ' '.TuteurUtils::getTelephones($tuteur);
            }
            $enfant->setTelephones($telephones);
        }
    }

    /**
     * @param Presence[] $presences
     *
     * @return ArrayCollection|Plaine[]
     */
    public static function extractPlainesFromPresences(array $presences): array
    {
        $plaines = new ArrayCollection();
        array_map(
            function ($presence) use ($plaines) {
                $jour = $presence->getJour();
                if (! $jour) {
                    return null;
                }
                $plaine = $jour->getPlaine();
                if (! $plaine instanceof Plaine) {
                    return null;
                }
                if (! $plaines->contains($plaine)) {
                    $plaines->add($plaine);
                }
            },
            $presences
        );

        return $plaines->toArray();
    }
}
