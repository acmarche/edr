<?php

namespace AcMarche\Edr\Jour\MessageHandler;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use AcMarche\Edr\Jour\Message\JourCreated;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;


#[AsMessageHandler]
final class JourCreatedHandler
{
    private readonly FlashBagInterface $flashBag;
    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()->getFlashBag();
    }
    public function __invoke(JourCreated $jourCreated): void
    {
        $this->flashBag->add('success', 'La date a bien été ajoutée');
    }
}
