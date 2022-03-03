<?php

namespace AcMarche\Edr\Mailer;

use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Mime\Header\Headers;
use Symfony\Component\Mime\Part\AbstractPart;
use Symfony\Component\Notifier\Notification\Notification;

class NotificationEmailJf extends NotificationEmail
{
    /**
     * Pour mettre important, car plus jolie.
     */
    public static function asPublicEmailJf(Headers $headers = null, AbstractPart $body = null): static
    {
        $email = new static($headers, $body);
        $email->markAsPublic();
        $email->importance(Notification::IMPORTANCE_HIGH);

        return $email;
    }

    /**
     * Pour retirer l'importance dans le sujet.
     */
    public function getPreparedHeaders(): Headers
    {
        $headers = parent::getPreparedHeaders();

        $headers->setHeaderBody('Text', 'Subject', $this->getSubject());

        return $headers;
    }
}
