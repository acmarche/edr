<?php

namespace AcMarche\Edr\Organisation\Message;

final class OrganisationCreated
{
    public function __construct(
        private readonly int $organisationId
    ) {
    }

    public function getOrganisationId(): int
    {
        return $this->organisationId;
    }
}
