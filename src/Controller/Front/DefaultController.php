<?php

namespace AcMarche\Edr\Controller\Front;

use AcMarche\Edr\Page\Factory\PageFactory;
use AcMarche\Edr\Page\Repository\PageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


final class DefaultController extends AbstractController
{
    public function __construct(
        private PageRepository $pageRepository,
        private PageFactory $pageFactory
    ) {
    }

    #[Route(path: '/', name: 'edr_front_home')]
    public function index(): Response
    {
        $homePage = $this->pageRepository->findHomePage();
        if (null === $homePage) {
            $homePage = $this->pageFactory->createHomePage();
        }

        return $this->render(
            '@AcMarcheEdr/default/index.html.twig',
            [
                'page' => $homePage,
            ]
        );
    }

    #[Route(path: '/menu/front', name: 'edr_front_menu_page')]
    public function menu(): Response
    {
        $pages = $this->pageRepository->findToDisplayMenu();

        return $this->render(
            '@AcMarcheEdr/front/_menu_top.html.twig',
            [
                'pages' => $pages,
            ]
        );
    }
}
