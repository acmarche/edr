<?php

namespace AcMarche\Edr\Reduction\Repository;

use AcMarche\Edr\Entity\Reduction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Reduction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reduction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reduction[]    findAll()
 * @method Reduction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class ReductionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Reduction::class);
    }

    public function remove(Reduction $reduction): void
    {
        $this->_em->remove($reduction);
    }

    public function flush(): void
    {
        $this->_em->flush();
    }

    public function persist(Reduction $reduction): void
    {
        $this->_em->persist($reduction);
    }
}
