<?php

namespace AcMarche\Edr\Plaine\Repository;

use AcMarche\Edr\Doctrine\OrmCrudTrait;
use AcMarche\Edr\Entity\Plaine\PlaineGroupe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PlaineGroupe|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlaineGroupe|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlaineGroupe[]    findAll()
 * @method PlaineGroupe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class PlaineGroupeRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, PlaineGroupe::class);
    }

    public function getQbForListing(): QueryBuilder
    {
        return $this->createQueryBuilder('plaine')
            ->orderBy('plaine.nom', 'ASC');
    }
}
