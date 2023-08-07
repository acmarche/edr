<?php

namespace AcMarche\Edr\Mailer;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Notification\EmailNotificationInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;
use Symfony\Component\Notifier\Recipient\RecipientInterface;

class EmailNotification extends Notification implements EmailNotificationInterface
{
    public function __construct(
        private readonly TemplatedEmail $templatedEmail,
        string $subject = '',
        array $channels = [
        ]
    )
    {
        parent::__construct($subject, $channels);
    }

    public function asEmailMessage(EmailRecipientInterface $recipient, string $transport = null): ?EmailMessage
    {
        return new EmailMessage($this->templatedEmail);
    }

    public function getChannels(RecipientInterface $recipient): array
    {
        $this->importance(Notification::IMPORTANCE_LOW);

        return ['email'];
    }
}
