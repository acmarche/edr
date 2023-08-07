<?php

namespace AcMarche\Edr\Controller\Admin;

use AcMarche\Edr\Entity\Organisation;
use AcMarche\Edr\Organisation\Form\OrganisationType;
use AcMarche\Edr\Organisation\Message\OrganisationCreated;
use AcMarche\Edr\Organisation\Message\OrganisationDeleted;
use AcMarche\Edr\Organisation\Message\OrganisationUpdated;
use AcMarche\Edr\Organisation\Repository\OrganisationRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/organisation')]
#[IsGranted(data: 'ROLE_MERCREDI_ADMIN')]
final class OrganisationController extends AbstractController
{
    public function __construct(
        private readonly OrganisationRepository $organisationRepository,
        private readonly MessageBusInterface $dispatcher
    ) {
    }

    #[Route(path: '/', name: 'edr_admin_organisation_index', methods: ['GET'])]
    public function index(): Response
    {
        if (($organisation = $this->organisationRepository->getOrganisation()) instanceof Organisation) {
            return $this->redirectToRoute('edr_admin_organisation_show', [
                'id' => $organisation->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/organisation/index.html.twig',
            [
                'organisation' => $organisation,
            ]
        );
    }

    #[Route(path: '/new', name: 'edr_admin_organisation_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        if (($organisation = $this->organisationRepository->getOrganisation()) instanceof Organisation) {
            $this->addFlash('danger', 'Une seule organisation peut être enregistrée');

            return $this->redirectToRoute('edr_admin_organisation_show', [
                'id' => $organisation->getId(),
            ]);
        }

        $organisation = new Organisation();
        $form = $this->createForm(OrganisationType::class, $organisation);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->organisationRepository->persist($organisation);
            $this->organisationRepository->flush();

            $this->dispatcher->dispatch(new OrganisationCreated($organisation->getId()));

            return $this->redirectToRoute('edr_admin_organisation_show', [
                'id' => $organisation->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/organisation/new.html.twig',
            [
                'organisation' => $organisation,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'edr_admin_organisation_show', methods: ['GET'])]
    public function show(Organisation $organisation): Response
    {
        return $this->render(
            '@AcMarcheEdrAdmin/organisation/show.html.twig',
            [
                'organisation' => $organisation,
            ]
        );
    }

    #[Route(path: '/{id}/edit', name: 'edr_admin_organisation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Organisation $organisation): Response
    {
        $form = $this->createForm(OrganisationType::class, $organisation);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->organisationRepository->flush();

            $this->dispatcher->dispatch(new OrganisationUpdated($organisation->getId()));

            return $this->redirectToRoute('edr_admin_organisation_show', [
                'id' => $organisation->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/organisation/edit.html.twig',
            [
                'organisation' => $organisation,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}/delete', name: 'edr_admin_organisation_delete', methods: ['POST'])]
    public function delete(Request $request, Organisation $organisation): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete' . $organisation->getId(), $request->request->get('_token'))) {
            $id = $organisation->getId();
            $this->organisationRepository->remove($organisation);
            $this->organisationRepository->flush();
            $this->dispatcher->dispatch(new OrganisationDeleted($id));
        }

        return $this->redirectToRoute('edr_admin_organisation_index');
    }
}
