<?php

namespace AcMarche\Edr\Scolaire\MessageHandler;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use AcMarche\Edr\Scolaire\Message\AnneeScolaireCreated;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;


#[AsMessageHandler]
final class AnneeScolaireCreatedHandler
{
    private readonly FlashBagInterface $flashBag;
    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()->getFlashBag();
    }
    public function __invoke(AnneeScolaireCreated $anneeScolaireCreated): void
    {
        $this->flashBag->add('success', "L'année a bien été ajoutée");
    }
}
