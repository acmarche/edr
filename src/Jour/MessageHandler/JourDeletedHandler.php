<?php

namespace AcMarche\Edr\Jour\MessageHandler;

use AcMarche\Edr\Jour\Message\JourDeleted;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class JourDeletedHandler implements MessageHandlerInterface
{
    private readonly FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()->getFlashBag();
    }

    public function __invoke(JourDeleted $jourDeleted): void
    {
        $this->flashBag->add('success', 'La date a bien été supprimée');
    }
}
