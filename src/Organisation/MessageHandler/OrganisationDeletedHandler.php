<?php

namespace AcMarche\Edr\Organisation\MessageHandler;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use AcMarche\Edr\Organisation\Message\OrganisationDeleted;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;


#[AsMessageHandler]
final class OrganisationDeletedHandler
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
