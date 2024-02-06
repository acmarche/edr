<?php

namespace AcMarche\Edr\Page\Factory;

use AcMarche\Edr\Entity\Page;
use AcMarche\Edr\Page\Repository\PageRepository;

final readonly class PageFactory
{
    public function __construct(
        private PageRepository $pageRepository
    ) {
    }

    public function createHomePage(): Page
    {
        $page = new Page();
        $page->setNom('Accueil');
        $page->setContent('Contenu à modifier');
        $page->setSlugSystem('home');

        $this->pageRepository->persist($page);
        $this->pageRepository->flush();

        return $page;
    }

    public function createContactPage(): Page
    {
        $page = new Page();
        $page->setNom('Nous contacter');
        $page->setContent('Contenu à modifier');
        $page->setSlugSystem('contact');

        $this->pageRepository->persist($page);
        $this->pageRepository->flush();

        return $page;
    }

    public function createModalitePage(): Page
    {
        $page = new Page();
        $page->setNom('Modalités pratiques');
        $page->setContent('Contenu à modifier');
        $page->setSlugSystem('modalites-pratiques');

        $this->pageRepository->persist($page);
        $this->pageRepository->flush();

        return $page;
    }
}
