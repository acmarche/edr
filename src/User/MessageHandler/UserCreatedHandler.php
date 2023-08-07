<?php

namespace AcMarche\Edr\User\MessageHandler;

use AcMarche\Edr\User\Message\UserCreated;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class UserCreatedHandler implements MessageHandlerInterface
{
    private readonly FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()->getFlashBag();
    }

    public function __invoke(UserCreated $userCreated): void
    {
        $this->flashBag->add('success', "L'utilisateur a bien été ajouté");
    }
}
