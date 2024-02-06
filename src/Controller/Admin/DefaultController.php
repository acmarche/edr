<?php

namespace AcMarche\Edr\Controller\Admin;

use AcMarche\Edr\Search\Form\SearchNameType;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[IsGranted('ROLE_MERCREDI_ADMIN')]
final class DefaultController extends AbstractController
{
    #[Route(path: '/', name: 'edr_admin_home')]
    public function default(): Response
    {
        $form = $this->createForm(SearchNameType::class);

        return $this->render(
            '@AcMarcheEdrAdmin/default/index.html.twig',
            [
                'form' => $form,
            ]
        );
    }
}
