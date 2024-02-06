<?php

namespace AcMarche\Edr\Sante\MessageHandler;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use AcMarche\Edr\Sante\Message\SanteQuestionUpdated;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;


#[AsMessageHandler]
final class SanteQuestionUpdatedHandler
{
    private readonly FlashBagInterface $flashBag;
    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()->getFlashBag();
    }
    public function __invoke(SanteQuestionUpdated $santeQuestionUpdated): void
    {
        $this->flashBag->add('success', 'La question a bien été modifiée');
    }
}
