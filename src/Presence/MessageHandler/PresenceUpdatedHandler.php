<?php

namespace AcMarche\Edr\Presence\MessageHandler;

use AcMarche\Edr\Presence\Message\PresenceUpdated;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class PresenceUpdatedHandler implements MessageHandlerInterface
{
    private readonly FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()->getFlashBag();
    }

    public function __invoke(PresenceUpdated $presenceUpdated): void
    {
        $this->flashBag->add('success', 'La présence a bien été modifiée');
    }
}
