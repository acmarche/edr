<?php

namespace AcMarche\Edr\Relation\MessageHandler;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use AcMarche\Edr\Relation\Message\RelationDeleted;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;


#[AsMessageHandler]
final class RelationDeletedHandler
{
    private readonly FlashBagInterface $flashBag;
    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()->getFlashBag();
    }
    public function __invoke(RelationDeleted $relationDeleted): void
    {
        $this->flashBag->add('success', 'La relation a bien été supprimée');
    }
}
