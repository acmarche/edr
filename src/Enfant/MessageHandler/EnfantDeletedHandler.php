<?php

namespace AcMarche\Edr\Enfant\MessageHandler;

use AcMarche\Edr\Enfant\Message\EnfantDeleted;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class EnfantDeletedHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()?->getFlashBag();
    }

    public function __invoke(EnfantDeleted $enfantDeleted): void
    {
        $this->flashBag->add('success', "L'enfant a bien été supprimé");
    }
}
