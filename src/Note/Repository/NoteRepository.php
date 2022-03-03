<?php

namespace AcMarche\Edr\Note\Repository;

use AcMarche\Edr\Entity\Note;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Note|null find($id, $lockMode = null, $lockVersion = null)
 * @method Note|null findOneBy(array $criteria, array $orderBy = null)
 * @method Note[]    findAll()
 * @method Note[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class NoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Note::class);
    }

    public function remove(Note $note): void
    {
        $this->_em->remove($note);
    }

    public function flush(): void
    {
        $this->_em->flush();
    }

    public function persist(Note $note): void
    {
        $this->_em->persist($note);
    }
}
