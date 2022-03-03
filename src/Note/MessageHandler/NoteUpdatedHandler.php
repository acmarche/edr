<?php

namespace AcMarche\Edr\Note\MessageHandler;

use AcMarche\Edr\Note\Message\NoteUpdated;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class NoteUpdatedHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()?->getFlashBag();
    }

    public function __invoke(NoteUpdated $noteUpdated): void
    {
        $this->flashBag->add('success', 'La note a bien été modifiée');
    }
}
