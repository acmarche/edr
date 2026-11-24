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

}
