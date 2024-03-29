<?php

namespace AcMarche\Edr\Controller\Admin;

use AcMarche\Edr\Entity\Plaine\Plaine;
use AcMarche\Edr\Plaine\Factory\PlainePdfFactory;
use AcMarche\Edr\Presence\Dto\ListingPresenceByMonth;
use AcMarche\Edr\Presence\Repository\PresenceRepository;
use AcMarche\Edr\Presence\Spreadsheet\SpreadsheetFactory;
use AcMarche\Edr\Search\SearchHelper;
use AcMarche\Edr\Utils\DateUtils;
use Exception;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[IsGranted('ROLE_MERCREDI_ADMIN')]
#[Route(path: '/export')]
final class ExportController extends AbstractController
{
    public function __construct(
        private readonly SpreadsheetFactory $spreadsheetFactory,
        private readonly ListingPresenceByMonth $listingPresenceByMonth,
        private readonly PresenceRepository $presenceRepository,
        private readonly PlainePdfFactory $plainePdfFactory,
        private readonly SearchHelper $searchHelper
    ) {
    }

    #[Route(path: '/presence', name: 'edr_admin_export_presence_xls')]
    public function default(): Response
    {
        $args = $this->searchHelper->getArgs(SearchHelper::PRESENCE_LIST);
        $jour = $args['jour'];
        $ecole = $args['ecole'];
        $presences = $this->presenceRepository->findPresencesByJourAndEcole($jour, $ecole);
        $spreadsheet = $this->spreadsheetFactory->presenceXls($presences);

        return $this->spreadsheetFactory->downloadXls($spreadsheet, 'listing-presences.xls');
    }

    /**
     * @param bool $one Office de l'enfance
     */
    #[Route(path: '/presence/mois/{one}', name: 'edr_admin_export_presence_mois_xls', requirements: [
        'mois' => '.+',
    ], methods: ['GET'])]
    public function presenceByMonthXls(bool $one): Response
    {
        $args = $this->searchHelper->getArgs(SearchHelper::PRESENCE_LIST_BY_MONTH);
        $mois = $args['mois'] ?? null;
        if (!$mois) {
            $this->addFlash('danger', 'Indiquez un mois');

            return $this->redirectToRoute('edr_admin_presence_by_month');
        }

        try {
            $date = DateUtils::createDateTimeFromDayMonth($mois);
        } catch (Exception $exception) {
            $this->addFlash('danger', $exception->getMessage());

            return $this->redirectToRoute('edr_admin_presence_by_month');
        }

        $fileName = 'listing-' . $date->format('m-Y') . '.xls';
        $listingPresences = $this->listingPresenceByMonth->create($date);
        if ($one) {
            $spreadsheet = $this->spreadsheetFactory->createXlsByMonthForOne($date, $listingPresences);
        } else {
            $spreadsheet = $this->spreadsheetFactory->createXlsByMonthDefault($listingPresences);
        }

        return $this->spreadsheetFactory->downloadXls($spreadsheet, $fileName);
    }

    #[Route(path: '/plaine/{id}/pdf', name: 'edr_admin_plaine_pdf')]
    public function plainePdf(Plaine $plaine): Response
    {
        return $this->plainePdfFactory->generate($plaine);
    }
}
