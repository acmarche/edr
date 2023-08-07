<?php

namespace AcMarche\Edr\Organisation\MessageHandler;

use AcMarche\Edr\Organisation\Message\OrganisationDeleted;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class OrganisationDeletedHandler implements MessageHandlerInterface
{
    private readonly FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()->getFlashBag();
    }

    public function __invoke(OrganisationDeleted $organisationDeleted): void
    {
        $this->flashBag->add('success', "L'organisation a bien été supprimée");
    }
}
