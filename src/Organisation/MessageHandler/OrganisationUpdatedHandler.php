<?php

namespace AcMarche\Edr\Organisation\MessageHandler;

use AcMarche\Edr\Organisation\Message\OrganisationUpdated;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class OrganisationUpdatedHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()?->getFlashBag();
    }

    public function __invoke(OrganisationUpdated $organisationUpdated): void
    {
        $this->flashBag->add('success', "L'organisation a bien été modifiée");
    }
}
