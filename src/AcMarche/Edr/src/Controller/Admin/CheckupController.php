<?php

namespace AcMarche\Edr\Controller\Admin;

use AcMarche\Edr\Enfant\Repository\EnfantRepository;
use AcMarche\Edr\Security\Role\EdrSecurityRole;
use AcMarche\Edr\Tuteur\Repository\TuteurRepository;
use AcMarche\Edr\User\Repository\UserRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/checkup')]
#[IsGranted('ROLE_MERCREDI_ADMIN')]
final class CheckupController extends AbstractController
{
    private $tutru = null;

    public function __construct(
        private readonly EnfantRepository $enfantRepository,
        private readonly TuteurRepository $tuteurRepository,
        private readonly UserRepository $userRepository
    ) {
    }

    #[Route(path: '/', name: 'edr_admin_checkup_index')]
    public function checkup(): Response
    {
        return $this->render(
            '@AcMarcheEdrAdmin/checkup/index.html.twig',
            [
            ]
        );
    }

    #[Route(path: '/orphelin', name: 'edr_admin_checkup_orphelin')]
    public function orphelin(): Response
    {
        return $this->render(
            '@AcMarcheEdrAdmin/checkup/orphelins.html.twig',
            [
                'enfants' => $this->enfantRepository->findOrphelins(),
            ]
        );
    }

    #[Route(path: '/sansenfants', name: 'edr_admin_checkup_sansenfant')]
    public function sansenfant(): Response
    {
        return $this->render(
            '@AcMarcheEdrAdmin/checkup/sansenfants.html.twig',
            [
                'tuteurs' => $this->tuteurRepository->findSansEnfants(),
            ]
        );
    }

    #[Route(path: '/plantage', name: 'edr_admin_plantage')]
    public function plantage(): Response
    {
        $this->tutru->getAll();

        return $this->render(
            '@AcMarcheEdrAdmin/default/index.html.twig',
            [
            ]
        );
    }

    #[Route(path: '/accounts', name: 'edr_admin_checkup_accounts')]
    public function accounts(): Response
    {
        $bad = [];
        $users = $this->userRepository->findAllOrderByNom();
        foreach ($users as $user) {
            if ($user->getRoles() < 1) {
                $bad[] = [
                    'error' => 'Aucun rôle',
                    'user' => $user,
                ];
                continue;
            }

            if ($user->hasRole(EdrSecurityRole::ROLE_PARENT) && 0 === \count($user->getTuteurs())) {
                $bad[] = [
                    'error' => 'Rôle parent, mais aucun parent associé',
                    'user' => $user,
                ];
                continue;
            }
        }

        return $this->render(
            '@AcMarcheEdrAdmin/checkup/accounts.html.twig',
            [
                'users' => $bad,
            ]
        );
    }

    #[Route(path: '/doublons', name: 'edr_admin_checkup_doublons')]
    public function doublon(): Response
    {
        $tuteurs = $this->tuteurRepository->findDoublon();
        $enfants = $this->enfantRepository->findDoublon();

        return $this->render(
            '@AcMarcheEdrAdmin/checkup/doublons.html.twig',
            [
                'tuteurs' => $tuteurs,
                'enfants' => $enfants,
            ]
        );
    }

}
