<?php

namespace AcMarche\Edr\Document\Repository;

use AcMarche\Edr\Entity\Document;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Document|null find($id, $lockMode = null, $lockVersion = null)
 * @method Document|null findOneBy(array $criteria, array $orderBy = null)
 * @method Document[]    findAll()
 * @method Document[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class DocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Document::class);
    }

    /**
     * @param $keyword
     *
     * @return Document[]
     */
    public function search(?string $keyword): array
    {
        return $this->createQueryBuilder('document')
            ->andWhere('document.nom LIKE :keyword OR document.prenom LIKE :keyword')
            ->setParameter('keyword', '%' . $keyword . '%')
            ->addOrderBy('document.nom', 'ASC')
            ->getQuery()->getResult();
    }

    public function remove(Document $document): void
    {
        $this->_em->remove($document);
    }

    public function flush(): void
    {
        $this->_em->flush();
    }

    public function persist(Document $document): void
    {
        $this->_em->persist($document);
    }

    public function getQbForListing(): QueryBuilder
    {
        return $this->createQueryBuilder('document')
            ->orderBy('document.nom', 'ASC');
    }
}
