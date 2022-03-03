<?php

namespace AcMarche\Edr\User\MessageHandler;

use AcMarche\Edr\User\Message\UserDeleted;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class UserDeletedHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()?->getFlashBag();
    }

    public function __invoke(UserDeleted $userDeleted): void
    {
        $this->flashBag->add('success', "L'utilisateur a bien été supprimé");
    }
}
