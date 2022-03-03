<?php

namespace AcMarche\Edr\Scolaire\MessageHandler;

use AcMarche\Edr\Scolaire\Message\GroupeScolaireUpdated;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class GroupeScolaireUpdatedHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()?->getFlashBag();
    }

    public function __invoke(GroupeScolaireUpdated $groupeScolaireUpdated): void
    {
        $this->flashBag->add('success', 'Le groupe a bien été modifié');
    }
}
