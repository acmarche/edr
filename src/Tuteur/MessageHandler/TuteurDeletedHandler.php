<?php

namespace AcMarche\Edr\Tuteur\MessageHandler;

use AcMarche\Edr\Tuteur\Message\TuteurDeleted;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class TuteurDeletedHandler implements MessageHandlerInterface
{
    private readonly FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()->getFlashBag();
    }

    public function __invoke(TuteurDeleted $tuteurDeleted): void
    {
        $this->flashBag->add('success', 'Le tuteur a bien été supprimé');
    }
}
