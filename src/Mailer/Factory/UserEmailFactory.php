<?php

namespace AcMarche\Edr\Mailer\Factory;

use AcMarche\Edr\Entity\Animateur;
use AcMarche\Edr\Entity\Security\User;
use AcMarche\Edr\Entity\Tuteur;
use AcMarche\Edr\Mailer\InitMailerTrait;
use AcMarche\Edr\Mailer\NotificationEmailJf;
use AcMarche\Edr\Organisation\Traits\OrganisationPropertyInitTrait;
use Symfony\Bridge\Twig\Mime\NotificationEmail;

class UserEmailFactory
{
    use InitMailerTrait;
    use OrganisationPropertyInitTrait;

    public function messageNewAccountToTuteur(User $user, Tuteur $tuteur, ?string $password = null): NotificationEmail
    {
        $from = $this->getEmailAddressOrganisation();

        $message = NotificationEmailJf::asPublicEmailJf();
        $message
            ->subject('informations sur votre compte de '.$this->organisation->getNom())
            ->from($from)
            ->to($user->getEmail())
            ->bcc($from)
            ->htmlTemplate('@AcMarcheEdrEmail/welcome/_mail_welcome_parent.html.twig')
            ->context(
                [
                    'tuteur' => $tuteur,
                    'user' => $user,
                    'password' => $password,
                    'footer_text' => 'orga',
                    'organisation' => $this->organisation,
                ]
            );

        return $message;
    }

    public function messageNewAccountToAnimateur(
        User $user,
        Animateur $animateur,
        ?string $password = null
    ): NotificationEmail {
        $from = $this->getEmailAddressOrganisation();

        $message = NotificationEmailJf::asPublicEmailJf();
        $message
            ->subject('informations sur votre compte de '.$this->organisation->getNom())
            ->from($from)
            ->to($user->getEmail())
            ->bcc($from)
            ->htmlTemplate('@AcMarcheEdrEmail/welcome/_mail_welcome_animateur.html.twig')
            ->context(
                [
                    'animateur' => $animateur,
                    'user' => $user,
                    'password' => $password,
                    'footer_text' => 'orga',
                    'organisation' => $this->organisation,
                ]
            );

        return $message;
    }
}
