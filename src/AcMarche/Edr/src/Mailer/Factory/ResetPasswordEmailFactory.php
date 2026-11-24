<?php

namespace AcMarche\Edr\Mailer\Factory;

use AcMarche\Edr\Entity\Security\User;
use AcMarche\Edr\Mailer\InitMailerTrait;
use AcMarche\Edr\Mailer\NotificationEmailJf;
use AcMarche\Edr\Organisation\Traits\OrganisationPropertyInitTrait;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Mime\Address;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken;

final class ResetPasswordEmailFactory
{
    use InitMailerTrait;
    use OrganisationPropertyInitTrait;

    public function messageSendLinkLostPassword(User $user, ResetPasswordToken $resetPasswordToken): NotificationEmail
    {
        $message = NotificationEmailJf::asPublicEmailJf();
        $message->from(new Address($this->organisation->getEmail(), $this->organisation->getNom()))
            ->to($user->getEmail())
            ->subject('Votre demande de changement de mot de passe')
            ->htmlTemplate('@AcMarcheEdrEmail/front/request_password.html.twig')
            ->context(
                [
                    'resetToken' => $resetPasswordToken,
                    'footer_text' => 'orga',
                    'organisation' => $this->organisation,
                ]
            );

        return $message;
    }
}
