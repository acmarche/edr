<?php

namespace AcMarche\Edr\Scolaire\Repository;

use AcMarche\Edr\Doctrine\OrmCrudTrait;
use AcMarche\Edr\Entity\Scolaire\AnneeScolaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AnneeScolaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method AnneeScolaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method AnneeScolaire[]    findAll()
 * @method AnneeScolaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class AnneeScolaireRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, AnneeScolaire::class);
    }

    /**
     * @return AnneeScolaire[]
     */
    public function findAllOrderByOrdre(): array
    {
        return $this->createQueryBuilder('annee_scolaire')
            ->orderBy('annee_scolaire.ordre', 'ASC')->getQuery()->getResult();
    }

    public function getQbForListing(): QueryBuilder
    {
        return $this->createQueryBuilder('annee_scolaire')
            ->orderBy('annee_scolaire.ordre', 'ASC');
    }
}
