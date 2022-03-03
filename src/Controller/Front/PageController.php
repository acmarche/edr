<?php

namespace AcMarche\Edr\Controller\Front;

use AcMarche\Edr\Contact\Form\ContactType;
use AcMarche\Edr\Entity\Page;
use AcMarche\Edr\Mailer\Factory\ContactEmailFactory;
use AcMarche\Edr\Mailer\NotificationMailer;
use AcMarche\Edr\Organisation\Repository\OrganisationRepository;
use AcMarche\Edr\Page\Factory\PageFactory;
use AcMarche\Edr\Page\Repository\PageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


final class PageController extends AbstractController
{
    public function __construct(
        private OrganisationRepository $organisationRepository,
        private PageRepository $pageRepository,
        private PageFactory $pageFactory,
        private ContactEmailFactory $contactEmailFactory,
        private NotificationMailer $notificationMailer
    ) {
    }

    #[Route(path: '/page/{slug}', name: 'edr_front_page_show')]
    public function page(Page $page): Response
    {
        if ('home' === $page->getSlugSystem()) {
            return $this->redirectToRoute('edr_front_home');
        }
        if ('contact' === $page->getSlugSystem()) {
            return $this->redirectToRoute('edr_front_contact');
        }

        return $this->render(
            '@AcMarcheEdr/front/page.html.twig',
            [
                'page' => $page,
            ]
        );
    }

    #[Route(path: '/contact', name: 'edr_front_contact')]
    public function contact(Request $request): Response
    {
        $page = $this->pageRepository->findContactPage();
        if (null === $page) {
            $page = $this->pageFactory->createContactPage();
        }
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $nom = $data['nom'];
            $email = $data['email'];
            $body = $data['texte'];

            $message = $this->contactEmailFactory->sendContactForm($email, $nom, $body);
            $this->notificationMailer->sendAsEmailNotification($message);
            $this->addFlash('success', 'Le message a bien été envoyé.');

            return $this->redirectToRoute('edr_front_contact');
        }

        return $this->render(
            '@AcMarcheEdr/front/contact.html.twig',
            [
                'page' => $page,
                'organisation' => $this->organisationRepository->getOrganisation(),
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/modalite', name: 'edr_front_modalite')]
    public function modalite(): Response
    {
        $page = $this->pageRepository->findModalitePage();
        if (null === $page) {
            $page = $this->pageFactory->createModalitePage();
        }

        return $this->render(
            '@AcMarcheEdr/front/page.html.twig',
            [
                'page' => $page,
            ]
        );
    }
}
