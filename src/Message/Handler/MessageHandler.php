<?php

namespace AcMarche\Edr\Message\Handler;

use AcMarche\Edr\Entity\Message;
use AcMarche\Edr\Entity\Plaine\Plaine;
use AcMarche\Edr\Mailer\InitMailerTrait;
use AcMarche\Edr\Mailer\NotificationMailer;
use AcMarche\Edr\Message\Factory\EmailFactory;
use AcMarche\Edr\Message\Repository\MessageRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

final class MessageHandler
{
    use InitMailerTrait;
    private FlashBagInterface $flashBag;

    public function __construct(
        private MessageRepository $messageRepository,
        private EmailFactory $emailFactory,
        private NotificationMailer $notificationMailer,
        RequestStack $requestStack
    ) {
        $this->flashBag = $requestStack->getSession()?->getFlashBag();
    }

    public function handle(Message $message): void
    {
        $templatedEmail = $this->emailFactory->create($message);

        foreach ($message->getDestinataires() as $addressEmail) {
            $templatedEmail->to($addressEmail);
            $this->notificationMailer->sendAsEmailNotification($templatedEmail, $addressEmail);
        }

        $this->messageRepository->persist($message);
        $this->messageRepository->flush();
    }

    public function handleFromPlaine(Plaine $plaine, Message $message, bool $attachCourrier): void
    {
        $templatedEmail = $this->emailFactory->createForPlaine($plaine, $message, $attachCourrier);

        foreach ($message->getDestinataires() as $addressEmail) {
            $templatedEmail->to($addressEmail);
            $this->notificationMailer->sendAsEmailNotification($templatedEmail);
        }

        $this->messageRepository->persist($message);
        $this->messageRepository->flush();
    }
}
