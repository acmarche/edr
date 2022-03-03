<?php

namespace AcMarche\Edr\Ecole\Repository;

use AcMarche\Edr\Entity\Scolaire\Ecole;
use AcMarche\Edr\Entity\Security\User;
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
     * @return Ecole[]
     */
    public function getEcolesByUser(User $user): array
    {
        return $this->createQueryBuilder('ecole')
            ->leftJoin('ecole.users', 'users', 'WITH')
            ->andWhere(':user MEMBER OF ecole.users')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
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

    public function remove(Ecole $ecole): void
    {
        $this->_em->remove($ecole);
    }

    public function flush(): void
    {
        $this->_em->flush();
    }

    public function persist(Ecole $ecole): void
    {
        $this->_em->persist($ecole);
    }

    public function getQbForListing(): QueryBuilder
    {
        return $this->createQueryBuilder('ecole')
            ->orderBy('ecole.nom', 'ASC');
    }
}
