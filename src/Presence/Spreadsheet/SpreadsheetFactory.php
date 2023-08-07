<?php

namespace AcMarche\Edr\Presence\Spreadsheet;

use AcMarche\Edr\Entity\Presence\Presence;
use AcMarche\Edr\Presence\Dto\ListingPresenceByMonth;
use AcMarche\Edr\Presence\Utils\PresenceUtils;
use AcMarche\Edr\Scolaire\Utils\ScolaireUtils;
use AcMarche\Edr\Spreadsheet\SpreadsheetDownloaderTrait;
use AcMarche\Edr\Utils\DateUtils;
use DateTime;
use DateTimeImmutable;
use IntlDateFormatter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

final class SpreadsheetFactory
{
    use SpreadsheetDownloaderTrait;
    /**
     * @var string
     */
    private const NOM = 'Nom';

    /**
     * @var string
     */
    private const FORMAT = 'd-m-Y';

    /**
     * @var int
     */
    private const COLONNE = 1;

    public function __construct(
        private readonly ScolaireUtils $scolaireUtils
    ) {
    }

    public function createXlsByMonthForOne(
        DateTime|DateTimeImmutable $dateTime,
        ListingPresenceByMonth $listingPresenceByMonth
    ): Spreadsheet {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();

        $datePeriod = DateUtils::getDatePeriod($dateTime);

        /**
         * titre de la feuille.
         */
        $ligne = 1;
        $worksheet
            ->setCellValue('A' . $ligne, self::NOM)
            ->setCellValue('B' . $ligne, 'Prénom')
            ->setCellValue('C' . $ligne, 'Né le')
            ->setCellValue('D' . $ligne, 'Groupe');

        $colonne = 'E';

        foreach ($datePeriod as $dateTime) {
            $worksheet->setCellValue($colonne . $ligne, DateUtils::formatFr($dateTime, IntlDateFormatter::SHORT));
            ++$colonne;
        }

        $ligne = 3;
        foreach ($listingPresenceByMonth->getEnfants() as $enfant) {
            $colonne = 'A';

            $neLe = null !== $enfant->getBirthday() ? $enfant->getBirthday()->format(self::FORMAT) : '';
            $groupe = $this->scolaireUtils->findGroupeScolaireEnfantByAnneeScolaire($enfant);

            $worksheet
                ->setCellValue($colonne . $ligne, $enfant->getNom())
                ->setCellValue(++$colonne . $ligne, $enfant->getPrenom())
                ->setCellValue(++$colonne . $ligne, $neLe)
                ->setCellValue(++$colonne . $ligne, $groupe->getNom());

            foreach ($datePeriod as $dateTime) {
                ++$colonne;
                $worksheet->setCellValue($colonne . $ligne, 1);
            }

            ++$ligne;
        }

        return $spreadsheet;
    }

    public function createXlsByMonthDefault(ListingPresenceByMonth $listingPresenceByMonth): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();

        /**
         * title.
         */
        $ligne = 1;
        $colonne = 'A';
        $worksheet
            ->setCellValue($colonne . $ligne, self::NOM)
            ->setCellValue(++$colonne . $ligne, 'Prénom')
            ->setCellValue(++$colonne . $ligne, 'Né le');

        foreach ($listingPresenceByMonth->getJoursListing() as $jourListing) {
            $worksheet->setCellValue(++$colonne . $ligne, $jourListing->getJour()->getDateJour()->format(self::FORMAT));
        }

        $worksheet->setCellValue(++$colonne . $ligne, 'Total');

        /**
         * Pour chaque enfant on affiche present ou pas et son total.
         */
        $ligne = 3;
        foreach ($listingPresenceByMonth->getEnfants() as $enfant) {
            $colonne = 'A';

            $neLe = null !== $enfant->getBirthday() ? $enfant->getBirthday()->format(self::FORMAT) : '';
            $worksheet
                ->setCellValue($colonne . $ligne, $enfant->getNom())
                ->setCellValue(++$colonne . $ligne, $enfant->getPrenom())
                ->setCellValue(++$colonne . $ligne, $neLe);

            $countPresences = 0;
            foreach ($listingPresenceByMonth->getJoursListing() as $jourListing) {
                $present = 0;
                if ($jourListing->hasEnfant($enfant)) {
                    $present = 1;
                    ++$countPresences;
                }

                $worksheet->setCellValue(++$colonne . $ligne, $present);
            }

            $worksheet->setCellValue(++$colonne . $ligne, $countPresences);
            ++$ligne;
        }

        /**
         * Total enfants et total enfants par jour.
         */
        $colonne = 'A';
        $worksheet->setCellValue($colonne . $ligne, \count($listingPresenceByMonth->getEnfants()) . ' enfants');
        $colonne = 'D';

        foreach ($listingPresenceByMonth->getJoursListing() as $jourListing) {
            $worksheet->setCellValue($colonne . $ligne, \count($jourListing->getEnfants()));
            ++$colonne;
        }

        $worksheet->setCellValue($colonne . $ligne, \count($listingPresenceByMonth->getPresences()));

        return $spreadsheet;
    }

    /**
     * @param Presence[] $presences
     */
    public function presenceXls(array $presences): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet
            ->setCellValue('A' . self::COLONNE, self::NOM)
            ->setCellValue('B' . self::COLONNE, 'Prénom')
            ->setCellValue('C' . self::COLONNE, 'Né le');

        $ligne = 3;

        $enfants = PresenceUtils::extractEnfants($presences);

        foreach ($enfants as $enfant) {
            $colonne = 'A';
            $neLe = null !== $enfant->getBirthday() ? $enfant->getBirthday()->format(self::FORMAT) : '';
            $worksheet
                ->setCellValue($colonne . $ligne, $enfant->getNom())
                ->setCellValue(++$colonne . $ligne, $enfant->getPrenom())
                ->setCellValue(++$colonne . $ligne, $neLe);
            ++$ligne;
        }

        return $spreadsheet;
    }
}
