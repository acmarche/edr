<?php

namespace AcMarche\Edr\Facture\Repository;

use AcMarche\Edr\Doctrine\OrmCrudTrait;
use AcMarche\Edr\Entity\Facture\FactureCron;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FactureCron|null find($id, $lockMode = null, $lockVersion = null)
 * @method FactureCron|null findOneBy(array $criteria, array $orderBy = null)
 * @method FactureCron[]    findAll()
 * @method FactureCron[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class FactureCronRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, FactureCron::class);
    }

    /**
     * @return array|FactureCron[]
     */
    public function findNotDone(): array
    {
        return $this->createQueryBuilder('cron')
            ->andWhere('cron.done = 0')
            ->getQuery()->getResult();
    }

    public function findOneByMonth(string $month): ?FactureCron
    {
        return $this->createQueryBuilder('cron')
            ->andWhere('cron.month = :month')
            ->setParameter('month', $month)
            ->getQuery()->getOneOrNullResult();
    }
}
