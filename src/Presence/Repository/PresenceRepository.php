<?php

namespace AcMarche\Edr\Presence\Repository;

use AcMarche\Edr\Doctrine\OrmCrudTrait;
use AcMarche\Edr\Entity\Enfant;
use AcMarche\Edr\Entity\Jour;
use AcMarche\Edr\Entity\Presence\Presence;
use AcMarche\Edr\Entity\Scolaire\Ecole;
use AcMarche\Edr\Entity\Tuteur;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Presence|null find($id, $lockMode = null, $lockVersion = null)
 * @method Presence|null findOneBy(array $criteria, array $orderBy = null)
 * @method Presence[]    findAll()
 * @method Presence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class PresenceRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Presence::class);
    }

    /**
     * @return Jour[]
     */
    public function findDaysRegisteredByEnfant(Enfant $enfant): array
    {
        $presences = $this->findByEnfant($enfant);
        $jours = [];
        foreach ($presences as $presence) {
            $jours[] = $presence->getJour();
        }

        return $jours;
    }

    /**
     * Pour le calcul du cout de la presence
     * On check s'il y a des frères et soeurs présents.
     */
    public function findByTuteurEnfantAndJour(Tuteur $tuteur, Enfant $enfant, Jour $jour): ?Presence
    {
        return $this->createQBl()
            ->andWhere('presence.enfant = :enfant')
            ->setParameter('enfant', $enfant)
            ->andWhere('presence.tuteur = :tuteur')
            ->setParameter('tuteur', $tuteur)
            ->andWhere('presence.jour = :jour')
            ->setParameter('jour', $jour)
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
    public function findByDays(array $days): array
    {
        return $this->createQBl()
            ->andWhere('presence.jour IN (:jours)')
            ->setParameter('jours', $days)
            ->addOrderBy('jour.date_jour')
            ->addOrderBy('enfant.nom')
            ->getQuery()->getResult();
    }

    /**
     * @return Presence[]
     */
    public function findByDay(Jour $jour): array
    {
        return $this->createQBl()
            ->andWhere('presence.jour = :jour')
            ->setParameter('jour', $jour)
            ->addOrderBy('enfant.nom')
            ->getQuery()->getResult();
    }

    /**
     * @return Presence[]
     */
    public function findByTuteurAndMonth(Tuteur $tuteur, ?DateTimeInterface $date = null): array
    {
        $qb = $this->createQBl()
            ->andWhere('presence.tuteur = :tuteur')
            ->setParameter('tuteur', $tuteur);

        if ($date instanceof DateTimeInterface) {
            $qb->andWhere('jour.date_jour LIKE :date')
                ->setParameter('date', $date->format('Y-m').'%');
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Quand on ajoute une présence.
     *
     * @throws NonUniqueResultException
     */
    public function isRegistered(Enfant $enfant, Jour $jour): ?Presence
    {
        return $this->createQBlPlaine()
            ->andWhere('presence.enfant = :enfant')
            ->setParameter('enfant', $enfant)
            ->andWhere('presence.jour = :jour')
            ->setParameter('jour', $jour)
            ->getQuery()->getOneOrNullResult();
    }

    /**
     * @return Presence[]
     */
    public function findPresencesByJourAndEcole(Jour $jour, ?Ecole $ecole): array
    {
        $queryBuilder = $this->createQBl();

        if ($jour) {
            $queryBuilder->andWhere('presence.jour = :jour')
                ->setParameter('jour', $jour);
        }

        if ($ecole instanceof Ecole) {
            $queryBuilder->andWhere('enfant.ecole = :ecole')
                ->setParameter('ecole', $ecole);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param array|Jour[] $jours
     *
     * @return Presence[]
     */
    public function findPresencesByJours(array $jours): array
    {
        return $this->createQBl()
            ->andWhere('presence.jour IN (:jours)')
            ->setParameter('jours', $jours)
            ->getQuery()->getResult();
    }

    /**
     * @return Presence[]
     */
    public function search(string $nom, Ecole $ecole, string $annee_scolaire): array
    {
        $queryBuilder = $this->createQBl();

        if ('' !== $nom) {
            $queryBuilder->andWhere('enfant.nom LIKE :nom')
                ->setParameter('nom', $nom);
        }

        if ($ecole) {
            $queryBuilder->andWhere('enfant.ecole = :ecole')
                ->setParameter('ecole', $ecole);
        }

        if ('' !== $annee_scolaire) {
            $queryBuilder->andWhere('enfant.annee_scolaire = :annee')
                ->setParameter('annee', $annee_scolaire);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    private function createQBlBase(): QueryBuilder
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

    private function createQBl(): QueryBuilder
    {
        return $this->createQBlBase()
            ->andWhere('jour.plaine IS NULL');
    }

    private function createQBlPlaine(): QueryBuilder
    {
        return $this->createQBlBase()
            ->andWhere('jour.plaine IS NOT NULL');
    }
}
