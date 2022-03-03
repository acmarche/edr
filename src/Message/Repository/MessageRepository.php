<?php

namespace AcMarche\Edr\Message\Repository;

use AcMarche\Edr\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Message::class);
    }

    public function remove(Message $message): void
    {
        $this->_em->remove($message);
    }

    public function flush(): void
    {
        $this->_em->flush();
    }

    public function persist(Message $message): void
    {
        $this->_em->persist($message);
    }
}
