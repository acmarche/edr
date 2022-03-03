<?php

namespace AcMarche\Edr\Document\MessageHandler;

use AcMarche\Edr\Document\Message\DocumentUpdated;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class DocumentUpdatedHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()?->getFlashBag();
    }

    public function __invoke(DocumentUpdated $documentUpdated): void
    {
        $this->flashBag->add('success', 'Le document a bien été modifié');
    }
}
