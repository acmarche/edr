<?php

namespace AcMarche\Edr\Mailer;

use AcMarche\Edr\Organisation\Traits\OrganisationPropertyInitTrait;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Notifier\Recipient\Recipient;

final class NotificationMailer
{
    use InitMailerTrait;
    use OrganisationPropertyInitTrait;

    public function sendAsEmailNotification(NotificationEmail $templatedEmail, ?string $email = null): void
    {
        if (! $email) {
            $email = $this->organisation->getEmail();
        }
        $recipient = new Recipient($email);
        $notification = new EmailNotification($templatedEmail);
        $this->sendNotifier($notification, $recipient);
    }
}
