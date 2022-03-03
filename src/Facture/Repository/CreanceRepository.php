<?php

namespace AcMarche\Edr\Facture\Repository;

use AcMarche\Edr\Doctrine\OrmCrudTrait;
use AcMarche\Edr\Entity\Facture\Creance;
use AcMarche\Edr\Entity\Tuteur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Creance|null find($id, $lockMode = null, $lockVersion = null)
 * @method Creance|null findOneBy(array $criteria, array $orderBy = null)
 * @method Creance[]    findAll()
 * @method Creance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class CreanceRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Creance::class);
    }

    /**
     * @return array|Creance[]
     */
    public function findByTuteur(Tuteur $tuteur): array
    {
        return $this->createQueryBuilder('creance')
            ->andWhere('creance.tuteur = :tuteur')
            ->setParameter('tuteur', $tuteur)
            ->addOrderBy('creance.createdAt', 'DESC')
            ->getQuery()->getResult();
    }
}
