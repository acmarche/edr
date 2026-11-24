<?php

namespace AcMarche\Edr\Page\Repository;

use AcMarche\Edr\Doctrine\OrmCrudTrait;
use AcMarche\Edr\Entity\Page;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Page|null find($id, $lockMode = null, $lockVersion = null)
 * @method Page|null findOneBy(array $criteria, array $orderBy = null)
 *                                                                                                 method Page[] findAll()
 * @method Page[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class PageRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    /**
     * @var string
     */
    private const SLUG_SYSTEM = 'slug_system';

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Page::class);
    }

    /**
     * @return Page[]
     */
    public function findAll(): array
    {
        return $this->findBy([], [
            'position' => 'ASC',
        ]);
    }

    /**
     * @return Page[]
     */
    public function findToDisplayMenu(): array
    {
        return $this->createQueryBuilder('page')
            ->andWhere('page.menu = 1')
            ->orderBy('page.position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findHomePage(): ?Page
    {
        return $this->findOneBy([
            self::SLUG_SYSTEM => 'home',
        ]);
    }

    public function findContactPage(): ?Page
    {
        return $this->findOneBy([
            self::SLUG_SYSTEM => 'contact',
        ]);
    }

    public function findModalitePage(): ?Page
    {
        return $this->findOneBy([
            self::SLUG_SYSTEM => 'modalites-pratiques',
        ]);
    }
}
