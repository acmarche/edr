<?php

namespace AcMarche\Edr\Controller\Animateur;

use AcMarche\Edr\Organisation\Traits\OrganisationPropertyInitTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted(data: 'ROLE_MERCREDI_ANIMATEUR')]
final class DefaultController extends AbstractController
{
    use OrganisationPropertyInitTrait;
    use GetAnimateurTrait;

    #[Route(path: '/', name: 'edr_animateur_home')]
    public function default(): Response
    {
        if (($response = $this->hasAnimateur()) instanceof Response) {
            return $response;
        }

        return $this->redirectToRoute('edr_animateur_enfant_index');
    }

    #[Route(path: '/nouveau', name: 'edr_animateur_nouveau')]
    public function nouveau(): Response
    {
        return $this->render(
            '@AcMarcheEdrAnimateur/default/nouveau.html.twig',
            [
                'organisation' => $this->organisation,
            ]
        );
    }
}
