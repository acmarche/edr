<?php

namespace AcMarche\Edr\Page\MessageHandler;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use AcMarche\Edr\Page\Message\PageUpdated;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;


#[AsMessageHandler]
final class PageUpdatedHandler
{
    private readonly FlashBagInterface $flashBag;
    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()->getFlashBag();
    }
    public function __invoke(PageUpdated $pageUpdated): void
    {
        $this->flashBag->add('success', 'La page a bien été modifiée');
    }
}
