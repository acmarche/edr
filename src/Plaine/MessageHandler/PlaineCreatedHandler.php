<?php

namespace AcMarche\Edr\Plaine\MessageHandler;

use AcMarche\Edr\Plaine\Message\PlaineCreated;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class PlaineCreatedHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()?->getFlashBag();
    }

    public function __invoke(PlaineCreated $plaineCreated): void
    {
        $this->flashBag->add('success', 'La plaine a bien été ajoutée');
    }
}
