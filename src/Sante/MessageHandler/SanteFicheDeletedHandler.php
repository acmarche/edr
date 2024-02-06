<?php

namespace AcMarche\Edr\Sante\MessageHandler;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use AcMarche\Edr\Sante\Message\SanteFicheDeleted;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;


#[AsMessageHandler]
final class SanteFicheDeletedHandler
{
    private readonly FlashBagInterface $flashBag;
    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()->getFlashBag();
    }
    public function __invoke(SanteFicheDeleted $santeFicheDeleted): void
    {
        $this->flashBag->add('success', 'La question a bien été supprimée');
    }
}
