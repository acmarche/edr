<?php

namespace AcMarche\Edr\Ecole\Repository;

use AcMarche\Edr\Doctrine\OrmCrudTrait;
use AcMarche\Edr\Entity\Scolaire\Ecole;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Ecole|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ecole|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ecole[]    findAll()
 * @method Ecole[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class EcoleRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Ecole::class);
    }

    public function findForAssociate(): QueryBuilder
    {
        return $this->createQueryBuilder('ecole')
            ->orderBy('ecole.nom');
    }

    /**
     * @return array|Ecole[]
     */
    public function findAllOrderByNom(): array
    {
        return $this->createQueryBuilder('ecole')
            ->orderBy('ecole.nom')->getQuery()
            ->getResult();
    }

    public function getQbForListing(): QueryBuilder
    {
        return $this->createQueryBuilder('ecole')
            ->orderBy('ecole.nom', 'ASC');
    }
}
