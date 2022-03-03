<?php

namespace AcMarche\Edr\Facture\Repository;

use AcMarche\Edr\Doctrine\OrmCrudTrait;
use AcMarche\Edr\Entity\Facture\FactureDecompte;
use AcMarche\Edr\Facture\FactureInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FactureDecompte|null find($id, $lockMode = null, $lockVersion = null)
 * @method FactureDecompte|null findOneBy(array $criteria, array $orderBy = null)
 * @method FactureDecompte[]    findAll()
 * @method FactureDecompte[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FactureDecompteRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FactureDecompte::class);
    }

    /**
     * @return array|FactureDecompte[]
     */
    public function findByFacture(FactureInterface $facture): array
    {
        return $this->createQueryBuilder('facture_decompte')
            ->andWhere('facture_decompte.facture = :fact')
            ->setParameter('fact', $facture)
            ->getQuery()->getResult();
    }
}
