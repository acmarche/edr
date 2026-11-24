<?php

namespace AcMarche\Edr\Tuteur\MessageHandler;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use AcMarche\Edr\Tuteur\Message\TuteurCreated;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;


#[AsMessageHandler]
final class TuteurCreatedHandler
{
    private readonly FlashBagInterface $flashBag;
    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()->getFlashBag();
    }
    public function __invoke(TuteurCreated $tuteurCreated): void
    {
        $this->flashBag->add('success', 'Le tuteur a bien été ajouté');
    }
}
