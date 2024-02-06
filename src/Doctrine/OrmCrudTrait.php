<?php

namespace AcMarche\Edr\Doctrine;

trait OrmCrudTrait
{
    public function insert(object $object): void
    {
        $this->persist($object);
        $this->flush();
    }

    public function persist(object $object): void
    {
        $this->getEntityManager()->persist($object);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function remove(object $object): void
    {
        $this->getEntityManager()->remove($object);
    }

    public function getOriginalEntityData(object $object)
    {
        return $this->getEntityManager()->getUnitOfWork()->getOriginalEntityData($object);
    }

}
