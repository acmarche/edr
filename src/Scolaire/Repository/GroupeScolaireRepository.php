<?php

namespace AcMarche\Edr\Scolaire\Repository;

use AcMarche\Edr\Doctrine\OrmCrudTrait;
use AcMarche\Edr\Entity\Scolaire\GroupeScolaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GroupeScolaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method GroupeScolaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method GroupeScolaire[]    findAll()
 * @method GroupeScolaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class GroupeScolaireRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, GroupeScolaire::class);
    }

    /**
     * @return GroupeScolaire[]
     */
    public function findAllOrderByOrdre(): array
    {
        return $this->createQueryBuilder('groupe_scolaire')
            ->addOrderBy('groupe_scolaire.is_plaine')
            ->addOrderBy('groupe_scolaire.ordre', 'ASC')
            ->addOrderBy('groupe_scolaire.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return GroupeScolaire[]
     */
    public function findAllForPlaineOrderByNom(): array
    {
        return $this->getQbForListingPlaine()
            ->orderBy('groupe_scolaire.nom', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return GroupeScolaire[]
     */
    public function findGroupesNotPlaine(): array
    {
        return $this->createQueryBuilder('groupe_scolaire')
            ->andWhere('groupe_scolaire.is_plaine != 1')
            ->orderBy('groupe_scolaire.nom', 'DESC')->getQuery()->getResult();
    }

    public function findGroupePlaineByAge(float $age): ?GroupeScolaire
    {
        return $this->createQueryBuilder('groupe_scolaire')
            ->andWhere('groupe_scolaire.is_plaine = 1')
            ->andWhere('groupe_scolaire.age_minimum <= :age AND groupe_scolaire.age_maximum >= :age')
            ->setParameter('age', $age)
            ->getQuery()->getOneOrNullResult();
    }

    public function getQbForListingPlaine(): QueryBuilder
    {
        return $this->createQueryBuilder('groupe_scolaire')
            ->andWhere('groupe_scolaire.is_plaine = 1')
            ->orderBy('groupe_scolaire.nom', 'DESC');
    }
}
