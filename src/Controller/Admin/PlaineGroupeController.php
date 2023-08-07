<?php

namespace AcMarche\Edr\Controller\Admin;

use AcMarche\Edr\Entity\Plaine\PlaineGroupe;
use AcMarche\Edr\Plaine\Form\PlaineGroupeEditType;
use AcMarche\Edr\Plaine\Repository\PlaineGroupeRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/plaine_groupe')]
#[IsGranted(data: 'ROLE_MERCREDI_ADMIN')]
final class PlaineGroupeController extends AbstractController
{
    public function __construct(
        private readonly PlaineGroupeRepository $plaineGroupeRepository
    ) {
    }

    #[Route(path: '/edit/{id}', name: 'edr_admin_plaine_groupe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PlaineGroupe $plaineGroupe): Response
    {
        $plaine = $plaineGroupe->getPlaine();
        $form = $this->createForm(PlaineGroupeEditType::class, $plaineGroupe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->plaineGroupeRepository->flush();
            $this->addFlash('success', 'le groupe été enregistré');

            return $this->redirectToRoute('edr_admin_plaine_show', [
                'id' => $plaine->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/plaine_groupe/edit.html.twig',
            [
                'plaine' => $plaine,
                'plaine_groupe' => $plaineGroupe,
                'form' => $form->createView(),
            ]
        );
    }
}
