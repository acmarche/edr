<?php

namespace AcMarche\Edr\Reduction\MessageHandler;

use AcMarche\Edr\Reduction\Message\ReductionUpdated;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class ReductionUpdatedHandler implements MessageHandlerInterface
{
    private readonly FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()->getFlashBag();
    }

    public function __invoke(ReductionUpdated $reductionUpdated): void
    {
        $this->flashBag->add('success', 'La réduction a bien été modifiée');
    }
}
