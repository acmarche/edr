<?php

namespace AcMarche\Edr\Accueil\MessageHandler;

use AcMarche\Edr\Accueil\Message\AccueilDeleted;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class AccueilDeletedHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()?->getFlashBag();
    }

    public function __invoke(AccueilDeleted $accueilDeleted): void
    {
        $this->flashBag->add('success', "L'acceuil a bien été supprimé");
    }
}
