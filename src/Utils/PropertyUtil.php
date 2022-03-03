<?php

namespace AcMarche\Edr\Utils;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\PropertyInfo\DoctrineExtractor;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

final class PropertyUtil
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function getProperties(string $className): ?array
    {
        $doctrineExtractor = new DoctrineExtractor($this->entityManager);

        return $doctrineExtractor->getProperties($className);
    }

    public function getPropertyAccessor(): PropertyAccessor
    {
        return PropertyAccess::createPropertyAccessor();
    }
}
