<?php

namespace AcMarche\Edr\Ecole\MessageHandler;

use AcMarche\Edr\Ecole\Message\EcoleDeleted;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class EcoleDeletedHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()?->getFlashBag();
    }

    public function __invoke(EcoleDeleted $ecoleDeleted): void
    {
        $this->flashBag->add('success', "L'école a bien été supprimée");
    }
}
