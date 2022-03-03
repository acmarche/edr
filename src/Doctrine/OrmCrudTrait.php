<?php

namespace AcMarche\Edr\Doctrine;

use Doctrine\ORM\EntityManager;

trait OrmCrudTrait
{
    /**
     * @var EntityManager
     */
    protected $_em;

    public function persist(object $entity)
    {
        $this->_em->persist($entity);
    }

    public function flush()
    {
        $this->_em->flush();
    }

    public function remove(object $entity)
    {
        $this->_em->remove($entity);
    }

    public function getOriginalEntityData(object $entity)
    {
        return $this->_em->getUnitOfWork()->getOriginalEntityData($entity);
    }
}
