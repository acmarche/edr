<?php

namespace AcMarche\Edr\Organisation\MessageHandler;

use AcMarche\Edr\Organisation\Message\OrganisationCreated;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class OrganisationCreatedHandler implements MessageHandlerInterface
{
    private readonly FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()->getFlashBag();
    }

    public function __invoke(OrganisationCreated $organisationCreated): void
    {
        $this->flashBag->add('success', "L'organisation a bien été ajoutée");
    }
}
