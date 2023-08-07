<?php

namespace AcMarche\Edr\Mailer\Factory;

use AcMarche\Edr\Contrat\Facture\FacturePdfPlaineInterface;
use AcMarche\Edr\Contrat\Facture\FacturePdfPresenceInterface;
use AcMarche\Edr\Entity\Facture\Facture;
use AcMarche\Edr\Facture\Factory\FactureFactory;
use AcMarche\Edr\Facture\FactureInterface;
use AcMarche\Edr\Mailer\InitMailerTrait;
use AcMarche\Edr\Mailer\NotificationEmailJf;
use AcMarche\Edr\Organisation\Traits\OrganisationPropertyInitTrait;
use AcMarche\Edr\Parameter\Option;
use AcMarche\Edr\Pdf\PdfDownloaderTrait;
use AcMarche\Edr\Tuteur\Utils\TuteurUtils;
use Exception;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Notifier\Notification\Notification;

class FactureEmailFactory
{
    use InitMailerTrait;
    use OrganisationPropertyInitTrait;
    use PdfDownloaderTrait;

    public function __construct(
        private readonly FacturePdfPresenceInterface $facturePdfPresence,
        private readonly FacturePdfPlaineInterface $facturePdfPlaine,
        private readonly FactureFactory $factureFactory,
        private readonly ParameterBagInterface $parameterBag
    ) {
    }

    public function initFromAndToForForm(?Facture $facture = null): array
    {
        $data = [];
        $data['from'] = $this->getEmailAddressOrganisation();
        if ($facture instanceof Facture) {
            $tuteur = $facture->getTuteur();
            if ($emails = TuteurUtils::getEmailsOfOneTuteur($tuteur)) {
                $data['to'] = $emails[0];
            }
        }

        return $data;
    }

    /**
     * @param Facture $from
     */
    public function messageFacture(string $from, string $sujet, string $body): NotificationEmailJf
    {
        $message = NotificationEmailJf::asPublicEmailJf();
        $message
            ->subject($sujet)
            ->from($from)
            ->htmlTemplate('@AcMarcheEdrEmail/admin/facture_mail.html.twig')
            ->textTemplate('@AcMarcheEdrEmail/admin/facture_mail.txt.twig')
            ->context(
                [
                    'importance' => Notification::IMPORTANCE_HIGH,
                    'texte' => $body,
                    'organisation' => $this->organisation,
                    'footer_text' => 'orga',
                ]
            );

        return $message;
    }

    public function setTos(NotificationEmail $message, array $tos): void
    {
        foreach ($tos as $email) {
            $message->addTo(new Address($email));
        }

        if ($this->parameterBag->has(Option::EMAILS_FACTURE)) {
            $copies = explode(',', $this->parameterBag->get(Option::EMAILS_FACTURE));
            if (\is_array($copies)) {
                foreach ($copies as $copy) {
                    if (filter_var($copy, FILTER_VALIDATE_EMAIL)) {
                        $message->addBcc(new Address($copy));
                    }
                }
            }
        }
    }

    /**
     * @throws Exception
     */
    public function attachFactureFromPath(NotificationEmail $message, Facture $facture): void
    {
        $path = $this->factureFactory->getBasePathFacture($facture->getMois());
        $factureFile = $path.'facture-'.$facture->getId().'.pdf';

        $date = $facture->getFactureLe();
        if (! is_readable($factureFile)) {
            throw new Exception('Pdf non trouvé '.$factureFile);
        }

        $message->attachFromPath($factureFile, 'facture_'.$date->format('d-m-Y').'.pdf', 'application/pdf');
    }

    /**
     * acces refuse wget https://assetx en console.
     */
    public function attachFactureOnTheFly(FactureInterface $facture, Email $message): void
    {
        $htmlInvoice = $this->factureFactory->createHtml($facture);
        $invoicepdf = $this->getPdf()->getOutputFromHtml($htmlInvoice);

        $date = $facture->getFactureLe();
        $message->attach($invoicepdf, 'facture_'.$date->format('d-m-Y').'.pdf', 'application/pdf');
    }

    public function attachFacturePlaineOnTheFly(FactureInterface $facture, Email $message): void
    {
        $htmlInvoice = $this->facturePdfPlaine->render($facture);
        $invoicepdf = $this->getPdf()->getOutputFromHtml($htmlInvoice);

        $date = $facture->getFactureLe();
        $message->attach($invoicepdf, 'facture_'.$date->format('d-m-Y').'.pdf', 'application/pdf');
    }
}
