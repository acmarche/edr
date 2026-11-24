<?php

namespace AcMarche\Edr\Ecole\MessageHandler;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use AcMarche\Edr\Ecole\Message\EcoleUpdated;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;


#[AsMessageHandler]
final class EcoleUpdatedHandler
{
    private readonly FlashBagInterface $flashBag;
    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()->getFlashBag();
    }
    public function __invoke(EcoleUpdated $ecoleUpdated): void
    {
        $this->flashBag->add('success', "L'école a bien été modifiée");
    }
}
