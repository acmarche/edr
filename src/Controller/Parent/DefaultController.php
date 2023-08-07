<?php

namespace AcMarche\Edr\Controller\Parent;

use AcMarche\Edr\Facture\Repository\FactureRepository;
use AcMarche\Edr\Organisation\Traits\OrganisationPropertyInitTrait;
use AcMarche\Edr\Relation\Utils\RelationUtils;
use AcMarche\Edr\Sante\Utils\SanteChecker;
use AcMarche\Edr\Tuteur\Utils\TuteurUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


final class DefaultController extends AbstractController
{
    use GetTuteurTrait;
    use OrganisationPropertyInitTrait;

    public function __construct(
        private readonly RelationUtils $relationUtils,
        private readonly SanteChecker $santeChecker,
        private readonly FactureRepository $factureRepository
    ) {
    }

    #[Route(path: '/', name: 'edr_parent_home')]
    #[IsGranted(data: 'ROLE_MERCREDI_PARENT')]
    public function default(): Response
    {
        if (($hasTuteur = $this->hasTuteur()) instanceof Response) {
            return $hasTuteur;
        }

        $enfants = $this->relationUtils->findEnfantsByTuteur($this->tuteur);
        $this->santeChecker->isCompleteForEnfants($enfants);
        $tuteurIsComplete = TuteurUtils::coordonneesIsComplete($this->tuteur);
        $factures = $this->factureRepository->findFacturesByTuteurWhoIsSend($this->tuteur);

        return $this->render(
            '@AcMarcheEdrParent/default/index.html.twig',
            [
                'enfants' => $enfants,
                'tuteur' => $this->tuteur,
                'factures' => $factures,
                'tuteurIsComplete' => $tuteurIsComplete,
                'year' => date('Y'),
            ]
        );
    }

    #[Route(path: '/nouveau', name: 'edr_parent_nouveau')]
    public function nouveau(): Response
    {
        return $this->render(
            '@AcMarcheEdrParent/default/nouveau.html.twig',
            [
                'organisation' => $this->organisation,
            ]
        );
    }
}
