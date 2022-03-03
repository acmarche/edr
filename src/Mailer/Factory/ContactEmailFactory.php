<?php

namespace AcMarche\Edr\Mailer\Factory;

use AcMarche\Edr\Mailer\InitMailerTrait;
use AcMarche\Edr\Mailer\NotificationEmailJf;
use AcMarche\Edr\Organisation\Traits\OrganisationPropertyInitTrait;
use Symfony\Bridge\Twig\Mime\NotificationEmail;

class ContactEmailFactory
{
    use InitMailerTrait;
    use OrganisationPropertyInitTrait;

    /**
     * @return NotificationEmail
     */
    public function sendContactForm(string $from, string $nom, string $body): NotificationEmailJf
    {
        $to = $this->getEmailAddressOrganisation();
        $message = NotificationEmailJf::asPublicEmailJf();

        $message
            ->subject('[Edr] '.$nom.' vous contact via le site')
            ->from($from)
            ->to($to)
            ->htmlTemplate('@AcMarcheEdrEmail/front/contact.html.twig')
            ->context(
                [
                    'body' => $body,
                ]
            );

        return $message;
    }
}
