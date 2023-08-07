<?php

namespace AcMarche\Edr\Page\MessageHandler;

use AcMarche\Edr\Page\Message\PageDeleted;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class PageDeletedHandler implements MessageHandlerInterface
{
    private readonly FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()->getFlashBag();
    }

    public function __invoke(PageDeleted $pageDeleted): void
    {
        $this->flashBag->add('success', 'La page a bien été supprimée');
    }
}
