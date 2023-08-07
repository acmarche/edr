<?php

namespace AcMarche\Edr\Plaine\MessageHandler;

use AcMarche\Edr\Plaine\Message\PlaineDeleted;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class PlaineDeletedHandler implements MessageHandlerInterface
{
    private readonly FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()->getFlashBag();
    }

    public function __invoke(PlaineDeleted $plaineDeleted): void
    {
        $this->flashBag->add('success', 'La plaine a bien été supprimée');
    }
}
