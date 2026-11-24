<?php

namespace AcMarche\Edr\Pdf;

use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Service\Attribute\Required;

trait PdfDownloaderTrait
{
    public Pdf $pdf;

    #[Required]
    public function setPdf(Pdf $pdf): void
    {
        $this->pdf = $pdf;
    }

    public function getPdf(): Pdf
    {
        return $this->pdf;
    }

    public function downloadPdf(string $html, string $fileName): Response
    {
        //debug
        // return new Response($html);

        return new PdfResponse(
            $this->pdf->getOutputFromHtml($html),
            $fileName
        );
    }
}
