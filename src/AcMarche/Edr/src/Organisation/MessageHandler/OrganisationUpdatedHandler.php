<?php

namespace AcMarche\Edr\Organisation\MessageHandler;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use AcMarche\Edr\Organisation\Message\OrganisationUpdated;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;


#[AsMessageHandler]
final class OrganisationUpdatedHandler
{
    private readonly FlashBagInterface $flashBag;
    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()->getFlashBag();
    }
    public function __invoke(OrganisationUpdated $organisationUpdated): void
    {
        $this->flashBag->add('success', "L'organisation a bien été modifiée");
    }
}
