<?php

namespace AcMarche\Edr\Plaine\Repository;

use AcMarche\Edr\Doctrine\OrmCrudTrait;
use AcMarche\Edr\Entity\Plaine\Plaine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Plaine|null find($id, $lockMode = null, $lockVersion = null)
 * @method Plaine|null findOneBy(array $criteria, array $orderBy = null)
 * @method Plaine[]    findAll()
 * @method Plaine[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class PlaineRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Plaine::class);
    }

    /**
     * @return array|Plaine[]
     */
    public function search(?string $nom, bool $archived = false): array
    {
        $qb = $this->createQueryBuilder('plaine')
            ->leftJoin('plaine.jours', 'jours', 'WITH')
            ->addSelect('jours')
            ->orderBy('jours.date_jour', 'DESC')
            ->andWhere('plaine.archived = :archive')
            ->setParameter('archive', $archived);

        if ($nom) {
            $qb->andWhere('plaine.nom LIKE :nom')
                ->setParameter('nom', '%'.$nom.'%');
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return array|Plaine[]
     */
    public function findPlaineByDateDesc(): array
    {
        return $this->createQueryBuilder('plaine')
            ->leftJoin('plaine.jours', 'jours', 'WITH')
            ->addSelect('jours')
            ->orderBy('jours.date_jour', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findPlaineOpen(?Plaine $plaine = null): ?Plaine
    {
        $qb = $this->createQueryBuilder('plaine')
            ->andWhere('plaine.inscriptionOpen = 1');

        if (null !== $plaine) {
            $qb->andWhere('plaine != :plaine')
                ->setParameter('plaine', $plaine);
        }

        return $qb->getQuery()
            ->getOneOrNullResult();
    }

    public function getQbForListing(): QueryBuilder
    {
        return $this->createQueryBuilder('plaine')
            ->andWhere('plaine.archived = 0')
            ->orderBy('plaine.nom', 'ASC');
    }
}
