<?php

namespace AcMarche\Edr\Scolaire\MessageHandler;

use AcMarche\Edr\Scolaire\Message\AnneeScolaireUpdated;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class AnneeScolaireUpdatedHandler implements MessageHandlerInterface
{
    private readonly FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()->getFlashBag();
    }

    public function __invoke(AnneeScolaireUpdated $anneeScolaireUpdated): void
    {
        $this->flashBag->add('success', "L'année a bien été modifiée");
    }
}
