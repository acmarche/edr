<?php

namespace AcMarche\Edr\Enfant\MessageHandler;

use AcMarche\Edr\Enfant\Message\EnfantCreated;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class EnfantCreatedHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()?->getFlashBag();
    }

    public function __invoke(EnfantCreated $enfantCreated): void
    {
        $this->flashBag->add('success', "L'enfant a bien été ajouté");
    }
}
