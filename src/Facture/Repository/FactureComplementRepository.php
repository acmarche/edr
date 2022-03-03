<?php

namespace AcMarche\Edr\Facture\Repository;

use AcMarche\Edr\Doctrine\OrmCrudTrait;
use AcMarche\Edr\Entity\Facture\FactureComplement;
use AcMarche\Edr\Facture\FactureInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FactureComplement|null find($id, $lockMode = null, $lockVersion = null)
 * @method FactureComplement|null findOneBy(array $criteria, array $orderBy = null)
 * @method FactureComplement[]    findAll()
 * @method FactureComplement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FactureComplementRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, FactureComplement::class);
    }

    /**
     * @return array|FactureComplement[]
     */
    public function findByFacture(FactureInterface $facture): array
    {
        return $this->createQueryBuilder('facture_complement')
            ->andWhere('facture_complement.facture = :fact')
            ->setParameter('fact', $facture)
            ->getQuery()->getResult();
    }
}
