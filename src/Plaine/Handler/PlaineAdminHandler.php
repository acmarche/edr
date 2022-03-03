<?php

namespace AcMarche\Edr\Plaine\Handler;

use AcMarche\Edr\Contrat\Facture\FactureHandlerInterface;
use AcMarche\Edr\Entity\Jour;
use AcMarche\Edr\Entity\Plaine\Plaine;
use AcMarche\Edr\Facture\FactureInterface;
use AcMarche\Edr\Jour\Repository\JourRepository;
use AcMarche\Edr\Plaine\Repository\PlainePresenceRepository;
use AcMarche\Edr\Plaine\Repository\PlaineRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

final class PlaineAdminHandler
{
    public function __construct(
        private PlaineRepository $plaineRepository,
        private JourRepository $jourRepository,
        private PlainePresenceRepository $plainePresenceRepository,
        private FactureHandlerInterface $factureHandler
    ) {
    }

    /**
     * Ajoute au moins 5 dates a la plaine.
     */
    public function initJours(Plaine $plaine): void
    {
        $currentJours = $this->jourRepository->findByPlaine($plaine);
        if ([] === $currentJours) {
            $plaine->addJour(new Jour(new DateTime('today')));
            for ($i = 1; $i < 5; ++$i) {
                $plaine->addJour(new Jour(new DateTime('+'.$i.' day')));
            }
        }
    }

    /**
     * @param Jour[]|Collection $newJours
     */
    public function handleEditJours(Plaine $plaine, array|Collection $newJours): void
    {
        foreach ($newJours as $jour) {
            if ($jour->getId()) {
                continue;
            }
            $jour->setPlaine($plaine);
            $this->jourRepository->persist($jour);
        }
        $this->removeJours($plaine, $newJours);
        $this->jourRepository->flush();
    }

    public function handleOpeningRegistrations(Plaine $plaine): ?Plaine
    {
        return $this->plaineRepository->findPlaineOpen($plaine);
    }

    /**
     * @param Jour[] $newJours
     */
    private function removeJours(Plaine $plaine, iterable $newJours): void
    {
        foreach ($this->jourRepository->findByPlaine($plaine) as $jour) {
            $found = false;
            foreach ($newJours as $newJour) {
                if ($jour->getDateJour()->format('Y-m-d') === $newJour->getDateJour()->format('Y-m-d')) {
                    $found = true;

                    break;
                }
            }
            if (! $found) {
                if ($presences = $this->plainePresenceRepository->findByDay($jour, $plaine)) {
                    foreach ($presences as $presence) {
                        if (! $this->factureHandler->isBilled($presence->getId(), FactureInterface::OBJECT_PLAINE)) {
                            $this->jourRepository->remove($presence);
                        }
                    }
                }
                $this->jourRepository->remove($jour);
            }
        }
    }
}
