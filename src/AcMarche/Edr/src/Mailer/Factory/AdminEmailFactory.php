<?php

namespace AcMarche\Edr\Mailer\Factory;

use AcMarche\Edr\Entity\Enfant;
use AcMarche\Edr\Entity\Security\User;
use AcMarche\Edr\Entity\Tuteur;
use AcMarche\Edr\Mailer\NotificationEmailJf;
use AcMarche\Edr\Organisation\Traits\OrganisationPropertyInitTrait;
use Symfony\Component\Security\Core\User\UserInterface;

class AdminEmailFactory
{
    use OrganisationPropertyInitTrait;

    /**
     * @param UserInterface|User $user
     */
    public function messageEnfantCreated(User|UserInterface $user, Enfant $enfant): NotificationEmailJf
    {
        $message = NotificationEmailJf::asPublicEmailJf();
        $message
            ->from($user->getEmail())
            ->to($this->organisation->getEmail())
            ->subject('Un enfant a été ajouté par ' . $user->getNom() . ' ' . $user->getPrenom())
            ->textTemplate('@AcMarcheEdrEmail/admin/_mail_add_enfant.html.twig')
            ->context(
                [
                    'user' => $user,
                    'enfant' => $enfant,
                    'footer_text' => 'orga',
                    'organisation' => $this->organisation,
                ]
            );

        return $message;
    }

    /**
     * @param array|Enfant[] $enfants
     */
    public function messagEnfantsOrphelins(array $enfants): NotificationEmailJf
    {
        $message = NotificationEmailJf::asPublicEmailJf();
        $email = $this->getEmailAddressOrganisation();
        $message
            ->from($email)
            ->to($email)
            ->subject('Des enfants orphelins ont été trouvés')
            ->textTemplate('@AcMarcheEdrEmail/admin/_mail_orphelins.html.twig')
            ->context(
                [
                    'enfants' => $enfants,
                    'footer_text' => 'orga',
                    'organisation' => $this->organisation,
                ]
            );

        return $message;
    }

    /**
     * @param array|Tuteur[] $tuteurs
     */
    public function messageTuteurArchived(array $tuteurs): NotificationEmailJf
    {
        $message = NotificationEmailJf::asPublicEmailJf();
        $email = $this->getEmailAddressOrganisation();
        $message
            ->from($email)
            ->to($email)
            ->subject('Les tuteurs ont été archivés')
            ->textTemplate('@AcMarcheEdrEmail/admin/_mail_tuteurs_archived.html.twig')
            ->context(
                [
                    'tuteurs' => $tuteurs,
                    'footer_text' => 'orga',
                    'organisation' => $this->organisation,
                ]
            );

        return $message;
    }

    /**
     * @param array|Tuteur[] $subject
     */
    public function messageAlert(string $subject, string $texte): NotificationEmailJf
    {
        $message = NotificationEmailJf::asPublicEmailJf();
        $email = $this->getEmailAddressOrganisation();
        $message
            ->from($email)
            ->to($email)
            ->subject($subject)
            ->content($texte);

        return $message;
    }
}
