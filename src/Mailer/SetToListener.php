<?php

namespace AcMarche\Edr\Mailer;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class SetToListener implements EventSubscriberInterface
{
    public function onMessage(MessageEvent $event): void
    {
        $email = $event->getMessage();
        if (! $email instanceof Email) {
            return;
        }
        //$email->bcc(new Address('xx@xx.be', 'Test mail'));
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MessageEvent::class => 'onMessage',
        ];
    }
}
