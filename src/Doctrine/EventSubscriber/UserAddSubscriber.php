<?php

namespace AcMarche\Edr\Doctrine\EventSubscriber;

use AcMarche\Edr\Utils\PropertyUtil;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Exception;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserAddSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly PropertyUtil $propertyUtil
    ) {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            //   Events::preUpdate,
        ];
    }

    public function prePersist(LifecycleEventArgs $lifecycleEventArgs): void
    {
        $object = $lifecycleEventArgs->getObject();
        if (!$this->propertyUtil->getPropertyAccessor()->isWritable($object, 'userAdd')) {
            return;
        }

        $this->setUserAdd($object);
    }

    private function setUserAdd(object $entity): void
    {
        //for loading fixtures
        if ($entity->getUserAdd()) {
            return;
        }

        $user = $this->security->getUser();

        if (!$user instanceof UserInterface) {
            throw new Exception('You must be login');
        }

        if ($user) {
            $entity->setUserAdd($user->getUserIdentifier());
        }
    }
}
