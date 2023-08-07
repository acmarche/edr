<?php

namespace AcMarche\Edr\Fixture;

use AcMarche\Edr\Accueil\Repository\AccueilRepository;
use AcMarche\Edr\Enfant\Repository\EnfantRepository;
use AcMarche\Edr\Presence\Repository\PresenceRepository;
use AcMarche\Edr\Tuteur\Repository\TuteurRepository;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Fidry\AliceDataFixtures\LoaderInterface;
use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

final class FixtureLoader
{
    public function __construct(
        private readonly LoaderInterface $loader,
        private readonly EntityManagerInterface $entityManager,
        private readonly ParameterBagInterface $parameterBag,
        private readonly TuteurRepository $tuteurRepository,
        private readonly PresenceRepository $presenceRepository,
        private readonly EnfantRepository $enfantRepository,
        private readonly AccueilRepository $accueilRepository
    ) {
    }

    public function getPath(): string
    {
        return $this->parameterBag->get('kernel.project_dir').'/src/AcMarche/Edr/src/Fixture/Files/';
    }

    public function load(): void
    {
        $path = $this->getPath();

        $files = [
            $path.'groupe_scolaire.yaml',
            $path.'annee_scolaire.yaml',
            $path.'document.yaml',
            $path.'ecole.yaml',
            $path.'tuteur.yaml',
            $path.'animateur.yaml',
            $path.'enfant.yaml',
            $path.'relation.yaml',
            $path.'user.yaml',
            $path.'question.yaml',
            $path.'reponse.yaml',
            $path.'sante_fiche.yaml',
            $path.'sante_reponse.yaml',
            $path.'organisation.yaml',
            $path.'page.yaml',
        /*    $path.'jour.yaml',
            $path.'presence.yaml',
            $path.'reduction.yaml',
            $path.'plaine.yaml',
            $path.'plaine_groupe.yaml',
            $path.'plaine_jour.yaml',
            $path.'accueil.yaml',
            $path.'note.yaml',*/
        ];

        $ormPurger = new ORMPurger($this->entityManager);
        //$ormPurger->setPurgeMode(1);
        $ormPurger->purge();

        $this->loader->load($files, [], [], PurgeMode::createDeleteMode());
        $tuteurSimposn = $this->tuteurRepository->findOneBy([
            'prenom' => 'Homer',
        ]);
        $enfant = $this->enfantRepository->findOneBy([
            'prenom' => 'Bart',
        ]);
        $presence = $this->presenceRepository->findOneBy([
            'tuteur' => $tuteurSimposn,
            'enfant' => $enfant,
        ]);
        $acceuil = $this->accueilRepository->findOneBy([
            'tuteur' => $tuteurSimposn,
            'enfant' => $enfant,
        ]);

        $this->loader->load(
            [
                $path.'facture.yaml',
            ],
            [],
            [
                'tuteur_Simpson' => $tuteurSimposn,
                'presence_bart_06_05' => $presence,
                'acceuil__bart_09_12' => $acceuil,
            ],
            PurgeMode::createNoPurgeMode()
        );
    }
}
