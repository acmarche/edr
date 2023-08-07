<?php

namespace AcMarche\Edr\Facture\MessageHandler;

use AcMarche\Edr\Facture\Message\FactureDeleted;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class FactureDeletedHandler implements MessageHandlerInterface
{
    private readonly FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()->getFlashBag();
    }

    public function __invoke(FactureDeleted $factureDeleted): void
    {
        $this->flashBag->add('success', 'La facture a bien été supprimée');
    }
}
