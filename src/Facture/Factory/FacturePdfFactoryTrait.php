<?php

namespace AcMarche\Edr\Facture\Factory;

use AcMarche\Edr\Contrat\Facture\FacturePdfPlaineInterface;
use AcMarche\Edr\Contrat\Facture\FacturePdfPresenceInterface;
use AcMarche\Edr\Entity\Facture\Facture;
use AcMarche\Edr\Facture\FactureInterface;
use AcMarche\Edr\Pdf\PdfDownloaderTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\Slugger\SluggerInterface;

final class FacturePdfFactoryTrait
{
    use PdfDownloaderTrait;

    public function __construct(private readonly FacturePdfPresenceInterface $facturePdfPresence, private readonly FacturePdfPlaineInterface $facturePdfPlaine, private readonly SluggerInterface $slugger)
    {
    }

    public function generate(FactureInterface $facture): Response
    {
        if ($facture->getPlaineNom()) {
            $html = $this->facturePdfPlaine->render($facture);
        } else {
            $html = $this->facturePdfPresence->render($facture);
        }

        $slug = $this->slugger->slug($facture->getNom() . ' ' . $facture->getPrenom());

        //   return new Response($html);

        return $this->downloadPdf($html, 'facture_' . $facture->getId() . '_' . $slug . '.pdf');
    }

    /**
     * @param array|Facture[] $factures
     */
    public function generates(array $factures, string $month): Response
    {
        $html = $this->facturePdfPresence->renderMultiple($factures);

        //  return new Response($html);

        return $this->downloadPdf($html, 'factures_' . $month . '.pdf');
    }
}
