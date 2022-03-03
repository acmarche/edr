<?php

namespace AcMarche\Edr\Sante\MessageHandler;

use AcMarche\Edr\Sante\Message\SanteFicheCreated;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class SanteFicheCreatedHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()?->getFlashBag();
    }

    public function __invoke(SanteFicheCreated $santeFicheCreated): void
    {
        $this->flashBag->add('success', 'La question a bien été ajoutée');
    }
}
