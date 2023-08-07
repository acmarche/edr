<?php

namespace AcMarche\Edr\Controller\Ecole;

use AcMarche\Edr\Entity\Enfant;
use AcMarche\Edr\Accueil\Repository\AccueilRepository;
use AcMarche\Edr\Enfant\Repository\EnfantRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AjaxController extends AbstractController
{
    public function __construct(
        private readonly AccueilRepository $accueilRepository,
        private readonly EnfantRepository $enfantRepository
    ) {
    }

    #[Route(path: '/accueil/ajax/duree', name: 'edr_ecole_ajax_duree', methods: ['POST'])]
    #[IsGranted(data: 'ROLE_MERCREDI_ECOLE')]
    public function updateDuree(Request $request): Response
    {
        $data = json_decode($request->getContent(), null, 512, JSON_THROW_ON_ERROR);
        $enfantId = $data->enfantId;
        $date = $data->date;
        $heure = $data->heure;
        $duree = $data->duree;
        if (!($enfant = $this->enfantRepository->find($enfantId)) instanceof Enfant) {
            return $this->json([
                'error' => 'Enfant non trouvé',
            ]);
        }

        $accueil = $this->accueilRepository->findByEnfantDateAndHeure($enfant, $date, $heure);

        return $this->json($data);

        return $this->json('<div class="alert alert-success">Tri enregistré</div>');
    }
}
