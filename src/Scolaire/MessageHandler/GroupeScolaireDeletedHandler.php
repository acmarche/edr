<?php

namespace AcMarche\Edr\Scolaire\MessageHandler;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use AcMarche\Edr\Scolaire\Message\GroupeScolaireDeleted;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;


#[AsMessageHandler]
final class GroupeScolaireDeletedHandler
{
    private readonly FlashBagInterface $flashBag;
    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()->getFlashBag();
    }
    public function __invoke(GroupeScolaireDeleted $groupeScolaireDeleted): void
    {
        $this->flashBag->add('success', 'Le groupe a bien été supprimé');
    }
}
