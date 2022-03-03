<?php

namespace AcMarche\Edr\Reduction\MessageHandler;

use AcMarche\Edr\Reduction\Message\ReductionDeleted;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class ReductionDeletedHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()?->getFlashBag();
    }

    public function __invoke(ReductionDeleted $reductionDeleted): void
    {
        $this->flashBag->add('success', 'La réduction a bien été supprimée');
    }
}
