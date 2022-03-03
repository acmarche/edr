<?php

namespace AcMarche\Edr\Spreadsheet;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

trait SpreadsheetDownloaderTrait
{
    public function downloadXls(Spreadsheet $spreadsheet, string $fileName): Response
    {
        $xlsx = new Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        // Create the excel file in the tmp directory of the system
        try {
            $xlsx->save($temp_file);
        } catch (Exception) {
        }

        $binaryFileResponse = new BinaryFileResponse($temp_file);
        $binaryFileResponse->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE,
            $fileName ?? $binaryFileResponse->getFile()->getFilename()
        );

        return $binaryFileResponse;
    }
}
