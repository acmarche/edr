<?php

namespace AcMarche\Edr\Scolaire\MessageHandler;

use AcMarche\Edr\Scolaire\Message\GroupeScolaireCreated;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class GroupeScolaireCreatedHandler implements MessageHandlerInterface
{
    private readonly FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()->getFlashBag();
    }

    public function __invoke(GroupeScolaireCreated $groupeScolaireCreated): void
    {
        $this->flashBag->add('success', 'Le groupe a bien été ajouté');
    }
}
