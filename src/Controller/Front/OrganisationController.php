<?php

namespace AcMarche\Edr\Controller\Front;

use AcMarche\Edr\Organisation\Repository\OrganisationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route(path: '/organisation')]
final class OrganisationController extends AbstractController
{
    public function __construct(
        private readonly OrganisationRepository $organisationRepository
    ) {
    }

    #[Route(path: '/show', name: 'edr_organisation_show')]
    public function organisation(): Response
    {
        $organisation = $this->organisationRepository->getOrganisation();

        return $this->render(
            '@AcMarcheEdr/organisation/_organisation.html.twig',
            [
                'organisation' => $organisation,
            ]
        );
    }

    #[Route(path: '/title', name: 'edr_organisation_title')]
    public function organisationTitle(): Response
    {
        $organisation = $this->organisationRepository->getOrganisation();

        return $this->render(
            '@AcMarcheEdr/organisation/_organisation_title.html.twig',
            [
                'organisation' => $organisation,
            ]
        );
    }

    #[Route(path: '/short', name: 'edr_organisation_short')]
    public function organisationShort(): Response
    {
        $organisation = $this->organisationRepository->getOrganisation();

        return $this->render(
            '@AcMarcheEdr/organisation/_organisation_short.html.twig',
            [
                'organisation' => $organisation,
            ]
        );
    }
}
