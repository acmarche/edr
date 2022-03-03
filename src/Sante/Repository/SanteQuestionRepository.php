<?php

namespace AcMarche\Edr\Sante\Repository;

use AcMarche\Edr\Doctrine\OrmCrudTrait;
use AcMarche\Edr\Entity\Sante\SanteQuestion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SanteQuestion|null find($id, $lockMode = null, $lockVersion = null)
 * @method SanteQuestion|null findOneBy(array $criteria, array $orderBy = null)
 * @method SanteQuestion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class SanteQuestionRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, SanteQuestion::class);
    }

    /**
     * @return SanteQuestion[]
     */
    public function findAllOrberByPosition(): array
    {
        return $this->findBy([], [
            'display_order' => 'ASC',
        ]);
    }

    /**
     * @param SanteQuestion[] $questionsRepondues
     *
     * @return SanteQuestion[]
     */
    public function getQuestionsNonRepondues($questionsRepondues): array
    {
        $queryBuilder = $this->createQueryBuilder('santeQuestion');

        if ([] !== $questionsRepondues) {
            $queryBuilder->andWhere('santeQuestion.id NOT IN (:questions) ')
                ->setParameter('questions', $questionsRepondues);
        }

        $queryBuilder->addOrderBy('santeQuestion.display_order');

        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }
}
