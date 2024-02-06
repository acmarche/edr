<?php

namespace AcMarche\Edr\Organisation\Message;

final readonly class OrganisationCreated
{
    public function __construct(
        private int $organisationId
    ) {
    }

    public function getOrganisationId(): int
    {
        return $this->organisationId;
    }
}
