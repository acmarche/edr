<?php

namespace AcMarche\Edr\Animateur\MessageHandler;

use AcMarche\Edr\Animateur\Message\AnimateurCreated;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class AnimateurCreatedHandler implements MessageHandlerInterface
{
    private readonly FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()->getFlashBag();
    }

    public function __invoke(AnimateurCreated $animateurCreated): void
    {
        $this->flashBag->add('success', 'L\' animateur a bien été ajouté');
    }
}
