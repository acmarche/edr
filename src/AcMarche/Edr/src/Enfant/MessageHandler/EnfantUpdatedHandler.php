<?php

namespace AcMarche\Edr\Enfant\MessageHandler;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use AcMarche\Edr\Enfant\Message\EnfantUpdated;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;


#[AsMessageHandler]
final class EnfantUpdatedHandler
{
    private readonly FlashBagInterface $flashBag;
    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()->getFlashBag();
    }
    public function __invoke(EnfantUpdated $enfantUpdated): void
    {
        $this->flashBag->add('success', "L'enfant a bien été modifié");
    }
}
