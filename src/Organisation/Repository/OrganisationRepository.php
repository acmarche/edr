<?php

namespace AcMarche\Edr\Organisation\Repository;

use AcMarche\Edr\Entity\Organisation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Organisation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Organisation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Organisation[]    findAll()
 * @method Organisation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class OrganisationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Organisation::class);
    }

    public function getOrganisation(): ?Organisation
    {
        return $this->createQueryBuilder('organisation')
            ->getQuery()->getOneOrNullResult();
    }

    public function remove(Organisation $organisation): void
    {
        $this->_em->remove($organisation);
    }

    public function flush(): void
    {
        $this->_em->flush();
    }

    public function persist(Organisation $organisation): void
    {
        $this->_em->persist($organisation);
    }
}
