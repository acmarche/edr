<?php

namespace AcMarche\Edr\Tuteur\MessageHandler;

use AcMarche\Edr\Tuteur\Message\TuteurUpdated;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class TuteurUpdatedHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()?->getFlashBag();
    }

    public function __invoke(TuteurUpdated $tuteurUpdated): void
    {
        $this->flashBag->add('success', 'Le tuteur a bien été modifié');
    }
}
