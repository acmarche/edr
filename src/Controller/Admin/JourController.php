<?php

namespace AcMarche\Edr\Controller\Admin;

use AcMarche\Edr\Contrat\Tarification\TarificationFormGeneratorInterface;
use AcMarche\Edr\Entity\Jour;
use AcMarche\Edr\Jour\Form\JourType;
use AcMarche\Edr\Jour\Form\SearchJourType;
use AcMarche\Edr\Jour\Message\JourCreated;
use AcMarche\Edr\Jour\Message\JourDeleted;
use AcMarche\Edr\Jour\Message\JourUpdated;
use AcMarche\Edr\Jour\Repository\JourRepository;
use AcMarche\Edr\Presence\Repository\PresenceRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/jour')]
#[IsGranted('ROLE_MERCREDI_ADMIN')]
final class JourController extends AbstractController
{
    public function __construct(
        private readonly JourRepository $jourRepository,
        private readonly TarificationFormGeneratorInterface $tarificationFormGenerator,
        private readonly PresenceRepository $presenceRepository,
        private readonly MessageBusInterface $dispatcher
    ) {
    }

    #[Route(path: '/', name: 'edr_admin_jour_index', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $form = $this->createForm(SearchJourType::class);
        $form->handleRequest($request);

        $archived = false;
        $pedagogique = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $archived = $data['archived'];
            $pedagogique = $data['pedagogique'];
        }

        $jours = $this->jourRepository->search($archived, $pedagogique);

        return $this->render(
            '@AcMarcheEdrAdmin/jour/index.html.twig',
            [
                'jours' => $jours,
                'form' => $form,
            ]
        );
    }

    #[Route(path: '/new', name: 'edr_admin_jour_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $jour = new Jour();
        $form = $this->createForm(JourType::class, $jour);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->jourRepository->persist($jour);
            $this->jourRepository->flush();

            $this->dispatcher->dispatch(new JourCreated($jour->getId()));

            return $this->redirectToRoute('edr_admin_jour_tarif', [
                'id' => $jour->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/jour/new.html.twig',
            [
                'jour' => $jour,
                'form' => $form,
            ]
        );
    }

    #[Route(path: '/tarif/{id}', name: 'edr_admin_jour_tarif', methods: ['GET', 'POST'])]
    public function tarif(Request $request, Jour $jour): Response
    {
        $form = $this->tarificationFormGenerator->generateForm($jour);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->jourRepository->persist($jour);
            $this->jourRepository->flush();

            $this->dispatcher->dispatch(new JourCreated($jour->getId()));

            return $this->redirectToRoute('edr_admin_jour_show', [
                'id' => $jour->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/jour/tarif.html.twig',
            [
                'jour' => $jour,
                'form' => $form,
            ]
        );
    }

    #[Route(path: '/{id}', name: 'edr_admin_jour_show', methods: ['GET'])]
    public function show(Jour $jour): Response
    {
        $tarifs = $this->tarificationFormGenerator->generateTarifsHtml($jour);
        $presences = $this->presenceRepository->findByDay($jour);

        return $this->render(
            '@AcMarcheEdrAdmin/jour/show.html.twig',
            [
                'jour' => $jour,
                'tarifs' => $tarifs,
                'presences' => $presences,
            ]
        );
    }

    #[Route(path: '/{id}/edit', name: 'edr_admin_jour_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Jour $jour): Response
    {
        $form = $this->createForm(JourType::class, $jour);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->jourRepository->flush();
            //todo switch pedagogique

            $this->dispatcher->dispatch(new JourUpdated($jour->getId()));

            return $this->redirectToRoute('edr_admin_jour_show', [
                'id' => $jour->getId(),
            ]);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/jour/edit.html.twig',
            [
                'jour' => $jour,
                'form' => $form,
            ]
        );
    }

    #[Route(path: '/{id}/delete', name: 'edr_admin_jour_delete', methods: ['POST'])]
    public function delete(Request $request, Jour $jour): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete' . $jour->getId(), $request->request->get('_token'))) {
            $jourId = $jour->getId();
            $this->jourRepository->remove($jour);
            $this->jourRepository->flush();
            $this->dispatcher->dispatch(new JourDeleted($jourId));
        }

        return $this->redirectToRoute('edr_admin_jour_index');
    }
}
