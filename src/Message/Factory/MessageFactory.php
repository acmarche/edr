<?php

namespace AcMarche\Edr\Message\Factory;

use AcMarche\Edr\Entity\Message;
use AcMarche\Edr\Organisation\Traits\OrganisationPropertyInitTrait;

final class MessageFactory
{
    use OrganisationPropertyInitTrait;

    public function createInstance(): Message
    {
        $message = new Message();
        $message->setFrom($this->organisation->getEmail());

        return $message;
    }
}
