<?php

namespace AcMarche\Edr\Controller\Admin;

use AcMarche\Edr\Accueil\Contrat\AccueilInterface;
use AcMarche\Edr\Accueil\Form\SearchAccueilForQuarter;
use AcMarche\Edr\Accueil\Repository\AccueilRepository;
use AcMarche\Edr\Utils\DateUtils;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/presence/one')]
#[IsGranted(data: 'ROLE_MERCREDI_ADMIN')]
final class PresenceOneController extends AbstractController
{
    public function __construct(
        private readonly AccueilRepository $accueilRepository
    ) {
    }

    /**
     * Liste toutes les presences par trimestre
     */
    #[Route(path: '/quarter', name: 'edr_admin_presence_by_quarter', methods: ['GET', 'POST'])]
    public function indexByQuarter(Request $request, ?int $num = null): Response
    {
        $form = $this->createForm(SearchAccueilForQuarter::class, ['year' => date('Y')]);
        $form->handleRequest($request);

        $childs = [];
        $data = [];
        $ages = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $dataForm = $form->getData();
            $ecole = $dataForm['ecole'];
            $trimestre = $dataForm['trimestre'];
            $year = $dataForm['year'];

            $months = match ($trimestre) {
                1 => [1, 2, 3],
                2 => [4, 5, 6],
                3 => [7, 8, 9],
                4 => [10, 11, 12],
                default => [],
            };

            $data = [];
            foreach ($months as $monthString) {
                try {
                    $month = DateUtils::createDateTimeFromDayMonth($monthString . '/' . $year);
                    $dataMonth = ['days' => []];
                    $totalByMonth = 0;
                    foreach (DateUtils::getAllDaysOfMonth($month) as $day) {
                        if (DateUtils::dayIsWeek($day)) {
                            $accueils = $this->accueilRepository->findByDateHeureAndEcole(
                                $day,
                                AccueilInterface::SOIR,
                                $ecole
                            );
                            $count = count($accueils);
                            $dataMonth['days'][$day->format('Y-m-d')] = $count;
                            $totalByMonth += $count;
                            foreach ($accueils as $accueil) {
                                $enfant = $accueil->getEnfant();
                                $childs[$enfant->getId()] = $enfant;
                            }
                        }
                    }

                    $dataMonth['total'] = $totalByMonth;
                    $data[$month->format('Y-m-d')] = $dataMonth;
                } catch (Exception $e) {
                    $this->addFlash('danger', $e->getMessage());
                }
            }

            $ages = [
                'all' => count($childs),
                'mat' => 0,
                'prim' => 0,
            ];

            $ref = DateUtils::createDateTimeFromDayMonth($months[0] . '/' . $year);
            foreach ($childs as $child) {
                if ($child->getAge($ref) > 6) {
                    ++$ages['prim'];
                } else {
                    ++$ages['mat'];
                }
            }
        }

        return $this->render(
            '@AcMarcheEdrAdmin/presence/index_by_quarter.html.twig',
            [
                'form' => $form->createView(),
                'data' => $data,
                'search' => $form->isSubmitted(),
                'ages' => $ages,
            ]
        );
    }
}
