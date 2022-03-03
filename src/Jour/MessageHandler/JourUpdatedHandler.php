<?php

namespace AcMarche\Edr\Jour\MessageHandler;

use AcMarche\Edr\Jour\Message\JourUpdated;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class JourUpdatedHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()?->getFlashBag();
    }

    public function __invoke(JourUpdated $jourUpdated): void
    {
        $this->flashBag->add('success', 'La date a bien été modifiée');
    }
}
