<?php

namespace AcMarche\Edr\Controller\Parent;

use AcMarche\Edr\Accueil\Calculator\AccueilCalculatorInterface;
use AcMarche\Edr\Entity\Presence\Accueil;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/accueil')]
#[IsGranted(data: 'ROLE_MERCREDI_PARENT')]
final class AccueilController extends AbstractController
{
    use GetTuteurTrait;

    public function __construct(
        private readonly AccueilCalculatorInterface $accueilCalculator
    ) {
    }

    #[Route(path: '/{uuid}', name: 'edr_parent_accueil_show', methods: ['GET'])]
    #[IsGranted(data: 'accueil_show', subject: 'accueil')]
    public function show(Accueil $accueil): Response
    {
        $cout = $this->accueilCalculator->calculate($accueil);

        return $this->render(
            '@AcMarcheEdrParent/accueil/show.html.twig',
            [
                'accueil' => $accueil,
                'cout' => $cout,
                'enfant' => $accueil->getEnfant(),
            ]
        );
    }
}
