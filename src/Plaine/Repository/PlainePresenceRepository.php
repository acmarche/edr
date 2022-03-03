<?php

namespace AcMarche\Edr\Plaine\Repository;

use AcMarche\Edr\Doctrine\OrmCrudTrait;
use AcMarche\Edr\Entity\Enfant;
use AcMarche\Edr\Entity\Jour;
use AcMarche\Edr\Entity\Plaine\Plaine;
use AcMarche\Edr\Entity\Presence\Presence;
use AcMarche\Edr\Entity\Tuteur;
use AcMarche\Edr\Presence\Utils\PresenceUtils;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

final class PlainePresenceRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(
        ManagerRegistry $managerRegistry
    ) {
        parent::__construct($managerRegistry, Presence::class);
    }

    /**
     * @return Enfant[]
     */
    public function findEnfantsByPlaine(Plaine $plaine): array
    {
        $presences = $this->findByPlaine($plaine);

        return PresenceUtils::extractEnfants($presences);
    }

    /**
     * @return Presence[]
     */
    public function findByPlaine(Plaine $plaine): array
    {
        $jours = $plaine->getJours();

        return $this->createQBl()
            ->andWhere('presence.jour IN (:jours)')
            ->setParameter('jours', $jours)
            ->addOrderBy('enfant.nom')
            ->getQuery()->getResult();
    }

    /**
     * @return Enfant[]
     */
    public function findEnfantsByPlaineAndTuteur(Plaine $plaine, Tuteur $tuteur): array
    {
        $presences = $this->findByPlaineAndTuteur($plaine, $tuteur);

        return PresenceUtils::extractEnfants($presences);
    }

    /**
     * @return Plaine[]|ArrayCollection
     */
    public function findPlainesByEnfant(Enfant $enfant): iterable
    {
        $presences = $this->findByEnfant($enfant);

        return PresenceUtils::extractPlainesFromPresences($presences);
    }

    /**
     * @return Enfant[]|ArrayCollection
     */
    public function findEnfantsByJour(Jour $jour, Plaine $plaine): array
    {
        $presences = $this->findByDay($jour, $plaine);

        return PresenceUtils::extractEnfants($presences);
    }

    /**
     * @return Presence[]
     */
    public function findByPlaineAndTuteur(Plaine $plaine, Tuteur $tuteur, bool $confirmed = null): array
    {
        $jours = $plaine->getJours();

        $qb = $this->createQBl()
            ->andWhere('presence.jour IN (:jours)')
            ->setParameter('jours', $jours)
            ->andWhere('presence.tuteur = :tuteur')
            ->setParameter('tuteur', $tuteur);

        if (null !== $confirmed) {
            $qb->andWhere('presence.confirmed = :confirmed')
                ->setParameter('confirmed', $confirmed);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Presence[]
     */
    public function findByPlaineAndEnfant(Plaine $plaine, Enfant $enfant): array
    {
        $jours = $plaine->getJours();

        return $this->createQBl()
            ->andWhere('presence.jour IN (:jours)')
            ->setParameter('jours', $jours)
            ->andWhere('presence.enfant = :enfant')
            ->setParameter('enfant', $enfant)
            ->getQuery()->getResult();
    }

    public function findOneByEnfantJour(Enfant $enfant, $jour): ?Presence
    {
        return $this->createQBl()
            ->andWhere('presence.jour = :jour')
            ->setParameter('jour', $jour)
            ->andWhere('presence.enfant = :enfant')
            ->setParameter('enfant', $enfant)
            ->getQuery()->getOneOrNullResult();
    }

    /**
     * @return Presence[]
     */
    public function findByEnfant(Enfant $enfant): array
    {
        return $this->createQBl()
            ->andWhere('presence.enfant = :enfant')
            ->setParameter('enfant', $enfant)
            ->getQuery()->getResult();
    }

    /**
     * @return Presence[]
     */
    public function findByDay($jour, Plaine $plaine): array
    {
        return $this->createQBl()
            ->andWhere('presence.jour = :jour')
            ->setParameter('jour', $jour)
            ->andWhere('jour.plaine = :plaine')
            ->setParameter('plaine', $plaine)
            ->addOrderBy('enfant.nom')
            ->getQuery()->getResult();
    }

    private function createQBl(): QueryBuilder
    {
        return $this->createQueryBuilder('presence')
            ->leftJoin('presence.jour', 'jour', 'WITH')
            ->leftJoin('presence.enfant', 'enfant', 'WITH')
            ->leftJoin('enfant.sante_fiche', 'sante_fiche', 'WITH')
            ->leftJoin('enfant.groupe_scolaire', 'groupe_scolaire', 'WITH')
            ->leftJoin('presence.tuteur', 'tuteur', 'WITH')
            ->leftJoin('jour.plaine', 'plaine', 'WITH')
            ->leftJoin('presence.reduction', 'reduction', 'WITH')
            ->addSelect('enfant', 'tuteur', 'sante_fiche', 'groupe_scolaire', 'jour', 'reduction', 'plaine');
    }
}
