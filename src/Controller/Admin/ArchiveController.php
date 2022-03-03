<?php

namespace AcMarche\Edr\Controller\Admin;

use AcMarche\Edr\Tuteur\Form\SearchTuteurType;
use AcMarche\Edr\Tuteur\Repository\TuteurRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/archive')]
#[IsGranted(data: 'ROLE_MERCREDI_ADMIN')]
class ArchiveController extends AbstractController
{
    public function __construct(private TuteurRepository $tuteurRepository)
    {
    }

    #[Route(path: '/', name: 'edr_admin_archive_tuteur', methods: ['GET'])]
    public function show(Request $request): Response
    {
        $form = $this->createForm(SearchTuteurType::class);
        $form->handleRequest($request);

        $tuteurs = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $tuteurs = $this->tuteurRepository->findArchived($data['nom']);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/archive/tuteurs.html.twig',
            [
                'tuteurs' => $tuteurs,
            ]
        );
    }
}