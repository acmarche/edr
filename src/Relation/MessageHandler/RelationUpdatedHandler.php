<?php

namespace AcMarche\Edr\Relation\MessageHandler;

use AcMarche\Edr\Relation\Message\RelationUpdated;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class RelationUpdatedHandler implements MessageHandlerInterface
{
    private readonly FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()->getFlashBag();
    }

    public function __invoke(RelationUpdated $relationCreated): void
    {
        $this->flashBag->add('success', 'La relation a bien été modifiée');
    }
}
