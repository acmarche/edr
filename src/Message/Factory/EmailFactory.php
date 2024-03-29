<?php

namespace AcMarche\Edr\Message\Factory;

use AcMarche\Edr\Entity\Message;
use AcMarche\Edr\Entity\Plaine\Plaine;
use AcMarche\Edr\Mailer\NotificationEmailJf;
use AcMarche\Edr\Organisation\Traits\OrganisationPropertyInitTrait;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Storage\StorageInterface;

final class EmailFactory
{
    use OrganisationPropertyInitTrait;

    public function __construct(
        private readonly StorageInterface $storage
    ) {
    }

    public function create(Message $message): NotificationEmailJf
    {
        $notification = NotificationEmailJf::asPublicEmailJf();
        $notification->subject($message->getSujet())
            ->from($message->getFrom())
            ->htmlTemplate('@AcMarcheEdrEmail/admin/mail.html.twig')
            ->context(
                [
                    'texte' => $message->getTexte(),
                    'organisation' => $this->organisation,
                ]
            );

        /*
         * Pieces jointes.
         */
        if (($uploadedFile = $message->getFile()) instanceof UploadedFile) {
            $notification->attachFromPath(
                $uploadedFile->getRealPath(),
                $uploadedFile->getClientOriginalName(),
                $uploadedFile->getClientMimeType()
            );
        }

        return $notification;
    }

    public function createForPlaine(Plaine $plaine, Message $message, bool $attachCourriers): NotificationEmailJf
    {
        $notification = NotificationEmailJf::asPublicEmailJf();
        $notification->subject($message->getSujet())
            ->from($message->getFrom())
            ->htmlTemplate('@AcMarcheEdrAdmin/admin/mail.html.twig')
            ->context(
                [
                    'texte' => $message->getTexte(),
                    'organisation' => $this->organisation,
                ]
            );

        /*
         * Pieces jointes.
         */
        if ($attachCourriers) {
            foreach ($plaine->getPlaineGroupes() as $plaineGroupe) {
                if ($plaineGroupe->getFileName()) {
                    $path = $this->storage->resolvePath($plaineGroupe, 'file');
                    $notification->attachFromPath(
                        $path,
                        $plaineGroupe->getGroupeScolaire()->getNom(),
                        $plaineGroupe->getMimeType()
                    );
                }
            }
        }

        return $notification;
    }
}
