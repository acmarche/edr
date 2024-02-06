<?php

namespace AcMarche\Edr\Sante\MessageHandler;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use AcMarche\Edr\Sante\Message\SanteFicheUpdated;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;


#[AsMessageHandler]
final class SanteFicheUpdatedHandler
{
    private readonly FlashBagInterface $flashBag;
    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()->getFlashBag();
    }
    public function __invoke(SanteFicheUpdated $santeFicheUpdated): void
    {
        $this->flashBag->add('success', 'Le formulaire santé a bien été enregistré');
    }
}
