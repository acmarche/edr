<?php

namespace AcMarche\Edr\Note\MessageHandler;

use AcMarche\Edr\Note\Message\NoteDeleted;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class NoteDeletedHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()?->getFlashBag();
    }

    public function __invoke(NoteDeleted $noteDeleted): void
    {
        $this->flashBag->add('success', 'La note a bien été supprimée');
    }
}
