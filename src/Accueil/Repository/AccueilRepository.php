<?php

namespace AcMarche\Edr\Accueil\Repository;

use AcMarche\Edr\Doctrine\OrmCrudTrait;
use AcMarche\Edr\Entity\Enfant;
use AcMarche\Edr\Entity\Presence\Accueil;
use AcMarche\Edr\Entity\Scolaire\Ecole;
use AcMarche\Edr\Entity\Tuteur;
use Carbon\CarbonPeriod;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Accueil|null find($id, $lockMode = null, $lockVersion = null)
 * @method Accueil|null findOneBy(array $criteria, array $orderBy = null)
 * @method Accueil[]    findAll()
 * @method Accueil[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class AccueilRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Accueil::class);
    }

    public function getQbForListing(): QueryBuilder
    {
        return $this->createQbl();
    }

    public function isRegistered(Accueil $accueil, Enfant $enfant): ?Accueil
    {
        return $this->createQbl()
            ->andWhere('accueil.enfant = :enfant')
            ->setParameter('enfant', $enfant)
            ->andWhere('accueil.date_jour = :date')
            ->setParameter('date', $accueil->getDateJour()->format('Y-m-d'))
            ->andWhere('accueil.heure = :heure')
            ->setParameter('heure', $accueil->getHeure())
            ->getQuery()->getOneOrNullResult();
    }

    /**
     * @return Accueil[]
     */
    public function findByEnfant(Enfant $enfant): array
    {
        return $this->createQbl()
            ->andWhere('accueil.enfant = :enfant')
            ->setParameter('enfant', $enfant)
            ->getQuery()->getResult();
    }

    /**
     * @return Accueil[]
     */
    public function findByEnfantAndDaysAndHeure(Enfant $enfant, CarbonPeriod $weekPeriod, string $heure): array
    {
        $days = array_map(static fn ($date) => $date->toDateString(), $weekPeriod->toArray());

        return $this->createQbl()
            ->andWhere('accueil.enfant = :enfant')
            ->setParameter('enfant', $enfant)
            ->andWhere('accueil.date_jour IN (:dates)')
            ->setParameter('dates', $days)
            ->andWhere('accueil.heure = :heure')
            ->setParameter('heure', $heure)
            ->getQuery()->getResult();
    }

    public function findOneByEnfantAndDayAndHour(Enfant $enfant, DateTimeInterface $date, string $heure): ?Accueil
    {
        return $this->createQbl()
            ->andWhere('accueil.enfant = :enfant')
            ->setParameter('enfant', $enfant)
            ->andWhere('accueil.date_jour LIKE :date')
            ->setParameter('date', $date->format('Y-m-d') . '%')
            ->andWhere('accueil.heure = :heure')
            ->setParameter('heure', $heure)
            ->getQuery()->getOneOrNullResult();
    }

    /**
     * @return Accueil[]
     */
    public function findByTuteur(Tuteur $tuteur): array
    {
        return $this->createQbl()
            ->andWhere('accueil.tuteur = :tuteur')
            ->setParameter('tuteur', $tuteur)
            ->getQuery()->getResult();
    }

    /**
     * @param array $ecoles
     *
     * @return Accueil[]
     */
    public function findByDateAndHeureAndEcoles(DateTimeInterface $date, ?string $heure, iterable $ecoles): array
    {
        $qb = $this->createQbl()
            ->andWhere('accueil.date_jour = :date')
            ->setParameter('date', $date);

        if ($heure) {
            $qb->andWhere('accueil.heure = :heure')
                ->setParameter('heure', $heure)
                ->getQuery()->getResult();
        }

        if ((is_countable($ecoles) ? \count($ecoles) : 0) > 0) {
            $qb->andWhere('ecole IN (:ecoles)')
                ->setParameter('ecoles', $ecoles)
                ->getQuery()->getResult();
        }

        return $qb->getQuery()->getResult();
    }

    public function findByEnfantDateAndHeure(Enfant $enfant, string $date, string $heure): ?Accueil
    {
        return $this->createQbl()
            ->andWhere('accueil.enfant = :enfant')
            ->setParameter('enfant', $enfant)
            ->andWhere('accueil.date_jour = :date')
            ->setParameter('date', $date)
            ->andWhere('accueil.heure = :heure')
            ->setParameter('heure', $heure)
            ->getQuery()->getOneOrNullResult();
    }

    /**
     * @return Accueil[]
     */
    public function findByDateHeureAndEcole(DateTimeInterface $date, ?string $heure, ?Ecole $ecole): array
    {
        $qb = $this->createQbl()
            ->andWhere('accueil.date_jour = :date')
            ->setParameter('date', $date->format('Y-m-d'));

        if ($heure) {
            $qb->andWhere('accueil.heure = :heure')
                ->setParameter('heure', $heure);
        }

        if ($ecole instanceof Ecole) {
            $qb->andWhere('enfant.ecole = :ecole')
                ->setParameter('ecole', $ecole);
        }

        $qb->orderBy('enfant.nom', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * @return array|Accueil[]
     */
    public function findByTuteurAndMonth(Tuteur $tuteur, ?DateTimeInterface $date = null): array
    {
        $qb = $this->createQbl()
            ->andWhere('accueil.tuteur = :tuteur')
            ->setParameter('tuteur', $tuteur);

        if ($date instanceof DateTimeInterface) {
            $qb->andWhere('accueil.date_jour LIKE :date')
                ->setParameter('date', $date->format('Y-m') . '%');
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return array|Accueil[]
     */
    public function findRetardByEnfant(Enfant $enfant): array
    {
        return $this->createQbl()
            ->andWhere('accueil.enfant = :enfant')
            ->setParameter('enfant', $enfant)
            ->getQuery()->getResult();
    }

    private function createQbl(): QueryBuilder
    {
        return $this->createQueryBuilder('accueil')
            ->leftJoin('accueil.enfant', 'enfant', 'WITH')
            ->leftJoin('accueil.tuteur', 'tuteur', 'WITH')
            ->leftJoin('enfant.ecole', 'ecole', 'WITH')
            ->addSelect('enfant', 'ecole', 'tuteur')
            ->orderBy('accueil.date_jour', 'DESC');
    }
}
