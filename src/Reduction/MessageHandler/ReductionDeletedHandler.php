<?php

namespace AcMarche\Edr\Reduction\MessageHandler;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use AcMarche\Edr\Reduction\Message\ReductionDeleted;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;


#[AsMessageHandler]
final class ReductionDeletedHandler
{
    private readonly FlashBagInterface $flashBag;
    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()->getFlashBag();
    }
    public function __invoke(ReductionDeleted $reductionDeleted): void
    {
        $this->flashBag->add('success', 'La réduction a bien été supprimée');
    }
}
