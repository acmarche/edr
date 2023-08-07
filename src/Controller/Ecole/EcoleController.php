<?php

namespace AcMarche\Edr\Controller\Ecole;

use AcMarche\Edr\Accueil\Contrat\AccueilInterface;
use AcMarche\Edr\Ecole\Repository\EcoleRepository;
use AcMarche\Edr\Enfant\Repository\EnfantRepository;
use AcMarche\Edr\Entity\Scolaire\Ecole;
use Carbon\Carbon;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/ecole')]
#[IsGranted(data: 'ROLE_MERCREDI_ECOLE')]
final class EcoleController extends AbstractController
{
    use GetEcolesTrait;

    public function __construct(
        private readonly EcoleRepository $ecoleRepository,
        private readonly EnfantRepository $enfantRepository
    ) {
    }

    #[Route(path: '/', name: 'edr_ecole_ecole_index', methods: ['GET'])]
    public function index(): Response
    {
        if (($response = $this->hasEcoles()) instanceof Response) {
            return $response;
        }

        $today = Carbon::today();

        return $this->render(
            '@AcMarcheEdrEcole/ecole/index.html.twig',
            [
                'ecoles' => $this->ecoles,
                'today' => $today,
                'heures' => AccueilInterface::HEURES,
            ]
        );
    }

    #[Route(path: '/{id}', name: 'edr_ecole_ecole_show', methods: ['GET'])]
    #[IsGranted(data: 'ecole_show', subject: 'ecole')]
    public function show(Ecole $ecole): Response
    {
        $enfants = $this->enfantRepository->findByEcolesForEcole([$ecole]);
        $today = Carbon::today();

        return $this->render(
            '@AcMarcheEdrEcole/ecole/show.html.twig',
            [
                'ecole' => $ecole,
                'enfants' => $enfants,
                'today' => $today,
                'heures' => AccueilInterface::HEURES,
            ]
        );
    }
}
