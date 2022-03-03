<?php

namespace AcMarche\Edr\Page\MessageHandler;

use AcMarche\Edr\Page\Message\PageCreated;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class PageCreatedHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()?->getFlashBag();
    }

    public function __invoke(PageCreated $pageCreated): void
    {
        $this->flashBag->add('success', 'La page a bien été ajoutée');
    }
}
