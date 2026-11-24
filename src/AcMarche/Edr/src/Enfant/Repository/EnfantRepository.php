<?php

namespace AcMarche\Edr\Enfant\Repository;

use AcMarche\Edr\Doctrine\OrmCrudTrait;
use AcMarche\Edr\Entity\Enfant;
use AcMarche\Edr\Entity\Scolaire\AnneeScolaire;
use AcMarche\Edr\Entity\Scolaire\Ecole;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Enfant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Enfant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Enfant[]    findAll()
 * @method Enfant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class EnfantRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Enfant::class);
    }

    /**
     * @return Enfant[]
     */
    public function findAllActif(): array
    {
        return $this->getNotArchivedQueryBuilder()->getQuery()->getResult();
    }

    /**
     * @param $keyword
     *
     * @return Enfant[]
     */
    public function findByName(string $keyword, bool $actif = true, int $max = 50): array
    {
        $queryBuilder = $this->getOrCreateQueryBuilder()
            ->andWhere('enfant.nom LIKE :keyword OR enfant.prenom LIKE :keyword')
            ->setParameter('keyword', '%' . $keyword . '%');

        if ($actif) {
            $queryBuilder->andWhere('enfant.archived = 0');
        }

        $queryBuilder->setMaxResults($max);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param array $ecoles
     *
     * @return Enfant[]
     */
    public function findByEcoles(iterable $ecoles): array
    {
        return $this->getNotArchivedQueryBuilder()
            ->andWhere('enfant.ecole IN (:ecoles)')
            ->setParameter('ecoles', $ecoles)
            ->getQuery()->getResult();
    }

    /**
     * @return Enfant[]
     */
    public function findOrphelins(): array
    {
        return $this->getOrCreateQueryBuilder()
            ->andWhere('enfant.relations IS EMPTY')
            ->getQuery()->getResult();
    }

    /**
     * @return Enfant[]
     */
    public function search(?string $nom, ?Ecole $ecole, ?AnneeScolaire $anneeScolaire, ?bool $archived): array
    {
        $queryBuilder = $this->getOrCreateQueryBuilder();

        if ($nom) {
            $queryBuilder->andWhere('enfant.nom LIKE :keyword OR enfant.prenom LIKE :keyword')
                ->setParameter('keyword', '%' . $nom . '%');
        }

        if ($ecole instanceof Ecole) {
            $queryBuilder->andWhere('ecole = :ecole')
                ->setParameter('ecole', $ecole);
        }

        if ($anneeScolaire instanceof AnneeScolaire) {
            $queryBuilder->andWhere('enfant.annee_scolaire = :annee')
                ->setParameter('annee', $anneeScolaire);
        }

        match ($archived) {
            true | false => $queryBuilder->andwhere('enfant.archived = :archive')
                ->setParameter('archive', $archived),
            default => $queryBuilder->andwhere('enfant.archived = :archive')
                ->setParameter('archive', 0),
        };

        return $queryBuilder->getQuery()->getResult();
    }

    public function findDoublon(): array
    {
        return $this->createQueryBuilder('enfant')
            ->select('count(enfant.nom) as lignes, enfant.nom, enfant.prenom')
            ->addGroupBy('enfant.nom')
            ->addGroupBy('enfant.prenom')
            ->getQuery()->getResult();
    }

    private function getOrCreateQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('enfant')
            ->leftJoin('enfant.ecole', 'ecole', 'WITH')
            ->leftJoin('enfant.annee_scolaire', 'annee_scolaire', 'WITH')
            ->leftJoin('enfant.sante_fiche', 'sante_fiche', 'WITH')
            ->leftJoin('enfant.relations', 'relations', 'WITH')
            ->addSelect('ecole', 'relations', 'sante_fiche', 'annee_scolaire')
            ->addOrderBy('enfant.nom', 'ASC');
    }

    private function getNotArchivedQueryBuilder(): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()
            ->andWhere('enfant.archived = 0');
    }
}
