<?php

namespace AcMarche\Edr\Jour\Repository;

use AcMarche\Edr\Doctrine\OrmCrudTrait;
use AcMarche\Edr\Entity\Animateur;
use AcMarche\Edr\Entity\Enfant;
use AcMarche\Edr\Entity\Jour;
use AcMarche\Edr\Entity\Plaine\Plaine;
use AcMarche\Edr\Presence\Repository\PresenceRepository;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Jour|null find($id, $lockMode = null, $lockVersion = null)
 * @method Jour|null findOneBy(array $criteria, array $orderBy = null)
 * @method Jour[]    findAll()
 * @method Jour[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class JourRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(
        ManagerRegistry $managerRegistry,
        private readonly PresenceRepository $presenceRepository
    ) {
        parent::__construct($managerRegistry, Jour::class);
    }

    public function getQlNotPlaine(bool $archive = false): QueryBuilder
    {
        return $this->createQueryBuilder('jour')
            ->leftJoin('jour.plaine', 'plaine', 'WITH')
            ->addSelect('plaine')
            ->andwhere('jour.archived = :archive')
            ->setParameter('archive', $archive)
            ->andWhere('jour.plaine IS NULL')
            ->addOrderBy('jour.date_jour', 'DESC');
    }

    public function getQbDaysNotRegisteredByEnfant(Enfant $enfant): QueryBuilder
    {
        $joursRegistered = $this->presenceRepository->findDaysRegisteredByEnfant(
            $enfant
        );

        $queryBuilder = $this->getQlNotPlaine();

        if ([] !== $joursRegistered) {
            $queryBuilder
                ->andWhere('jour.id NOT IN (:jours)')
                ->setParameter('jours', $joursRegistered);
        }

        return $queryBuilder;
    }

    /**
     * @return Jour[]
     */
    public function findDaysByMonth(DateTimeInterface $dateTime): array
    {
        return $this->createQueryBuilder('jour')
            ->leftJoin('jour.plaine', 'plaine', 'WITH')
            ->addSelect('plaine')
            ->andWhere('jour.date_jour LIKE :date')
            ->setParameter('date', $dateTime->format('Y-m') . '%')
            ->addOrderBy('jour.date_jour', 'ASC')
            ->andWhere('plaine IS NULL')
            ->getQuery()->getResult();
    }

    /**
     * @return Jour[]
     */
    public function search(bool $archive, ?bool $pedagogique): array
    {
        $qb = $this->getQlNotPlaine($archive);
        switch ($pedagogique) {
            case true | false:
                $qb->andwhere('jour.pedagogique = :pedagogique')
                    ->setParameter('pedagogique', $pedagogique);
                break;
            default:
                break;
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Jour[]
     */
    public function findNotArchived(): array
    {
        return $this->getQlNotPlaine()
            ->getQuery()->getResult();
    }

    public function getQbForListingAnimateur(Animateur $animateur): QueryBuilder
    {
        return $this->getQlNotPlaine()
            ->andWhere('plaineJour IS NULL')
            ->andWhere(':animateur MEMBER OF jour.animateurs')
            ->setParameter('animateur', $animateur)
            ->orderBy('jour.date_jour', 'DESC');
    }

    /**
     * @return Jour[]
     */
    public function findPedagogiqueByDateGreatherOrEqualAndNotRegister(
        DateTimeInterface $dateTime,
        Enfant $enfant
    ): array {
        return $this->getQbDaysNotRegisteredByEnfant($enfant)
            ->andWhere('jour.date_jour >= :date')
            ->setParameter('date', $dateTime->format('Y-m-d') . '%')
            ->andWhere('jour.pedagogique = 1')
            ->getQuery()->getResult();
    }

    /**
     * @return Jour[]
     */
    public function findJourNotPedagogiqueByDateGreatherOrEqualAndNotRegister(
        DateTimeInterface $dateTime,
        Enfant $enfant
    ): array {
        return $this->getQbDaysNotRegisteredByEnfant($enfant)
            ->andWhere('jour.date_jour >= :date')
            ->setParameter('date', $dateTime->format('Y-m-d') . '%')
            ->andWhere('jour.pedagogique = 0')
            ->getQuery()->getResult();
    }

    public function getQlJourByDateGreatherOrEqualAndNotRegister(
        Enfant $enfant,
        DateTimeInterface $dateTime
    ): QueryBuilder {
        return $this->getQbDaysNotRegisteredByEnfant($enfant)
            ->andWhere('jour.date_jour >= :date')
            ->setParameter('date', $dateTime->format('Y-m-d') . '%');
    }

    /**
     * use in Handler plaine.
     *
     * @throws NonUniqueResultException
     */
    public function findOneByDateTimeAndPlaine(DateTimeInterface $dateTime, Plaine $plaine): ?Jour
    {
        return $this->createQueryBuilder('jour')
            ->andWhere('jour.date_jour LIKE :date')
            ->setParameter('date', $dateTime->format('Y-m-d') . '%')
            ->andWhere('jour.plaine = :plaine')
            ->setParameter('plaine', $plaine)
            ->getQuery()->getOneOrNullResult();
    }

    /**
     * @return array|Jour[]
     */
    public function findByAnimateur(Animateur $animateur): array
    {
        return $this->getQlNotPlaine()
            ->andWhere(':animateur MEMBER OF jour.animateurs')
            ->setParameter('animateur', $animateur)
            ->getQuery()->getResult();
    }

    /**
     * @return array|Jour[]
     */
    public function findByPlaine(Plaine $plaine): array
    {
        return $this->createQueryBuilder('jour')
            ->leftJoin('jour.plaine', 'plaine', 'WITH')
            ->addSelect('plaine')
            ->setParameter('plaine', $plaine)
            ->andWhere('jour.plaine = :plaine')
            ->addOrderBy('jour.date_jour', 'ASC')
            ->getQuery()->getResult();
    }
}
