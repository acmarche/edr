<?php

namespace AcMarche\Edr\Scolaire\MessageHandler;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use AcMarche\Edr\Scolaire\Message\GroupeScolaireCreated;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;


#[AsMessageHandler]
final class GroupeScolaireCreatedHandler
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
