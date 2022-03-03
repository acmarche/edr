<?php

namespace AcMarche\Edr\Facture\MessageHandler;

use AcMarche\Edr\Facture\Message\FactureUpdated;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class FactureUpdatedHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()?->getFlashBag();
    }

    public function __invoke(FactureUpdated $factureUpdated): void
    {
        $this->flashBag->add('success', 'La facture a bien été modifiée');
    }
}
