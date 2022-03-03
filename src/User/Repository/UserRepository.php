<?php

namespace AcMarche\Edr\User\Repository;

use AcMarche\Edr\Doctrine\OrmCrudTrait;
use AcMarche\Edr\Entity\Security\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, User::class);
    }

    /**
     * @see UserProviderListener::checkPassport
     *
     * @return int|mixed|string|null
     *
     * @throws NonUniqueResultException
     */
    public function loadUserByIdentifier(string $username)
    {
        return $this->createQueryBuilder('user')
            ->andWhere('user.email = :username OR user.username = :username')
            ->setParameter('username', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return User[]
     */
    public function findAllOrderByNom(): array
    {
        return $this->createQueryBuilder('user')
            ->leftJoin('user.tuteurs', 'tuteurs', 'WITH')
            ->leftJoin('user.ecoles', 'ecoles', 'WITH')
            ->leftJoin('user.animateurs', 'animateurs', 'WITH')
            ->addSelect('tuteurs', 'ecoles', 'animateurs')
            ->addOrderBy('user.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (! $user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function findOneByEmailOrUserName(string $username): ?User
    {
        return $this->createQueryBuilder('user')
            ->andWhere('user.email = :username OR user.username = :username')
            ->setParameter('username', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return User[]
     */
    public function findByNameOrRoles(?string $name, ?string $role): array
    {
        $queryBuilder = $this->createQueryBuilder('user')
            ->leftJoin('user.ecoles', 'ecoles', 'WITH')
            ->leftJoin('user.animateurs', 'animateurs', 'WITH')
            ->leftJoin('user.tuteurs', 'tuteurs', 'WITH')
            ->addSelect('ecoles', 'animateurs', 'tuteurs');

        if ($name) {
            $queryBuilder->andWhere('user.nom LIKE :nom OR user.prenom LIKE :nom OR user.email LIKE :nom ')
                ->setParameter('nom', '%'.$name.'%');
        }

        if ($role) {
            $queryBuilder->andWhere('user.roles LIKE :role')
                ->setParameter('role', '%'.$role.'%');
        }

        return $queryBuilder
            ->addOrderBy('user.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
