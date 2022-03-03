<?php

namespace AcMarche\Edr\Reduction\MessageHandler;

use AcMarche\Edr\Reduction\Message\ReductionCreated;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class ReductionCreatedHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()?->getFlashBag();
    }

    public function __invoke(ReductionCreated $reductionCreated): void
    {
        $this->flashBag->add('success', 'La réduction a bien été ajoutée');
    }
}
