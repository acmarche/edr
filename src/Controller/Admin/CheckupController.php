<?php

namespace AcMarche\Edr\Controller\Admin;

use AcMarche\Edr\Contrat\Facture\FactureCalculatorInterface;
use AcMarche\Edr\Contrat\Presence\PresenceCalculatorInterface;
use AcMarche\Edr\Enfant\Repository\EnfantRepository;
use AcMarche\Edr\Facture\FactureInterface;
use AcMarche\Edr\Facture\Repository\FacturePresenceRepository;
use AcMarche\Edr\Facture\Repository\FactureRepository;
use AcMarche\Edr\Jour\Repository\JourRepository;
use AcMarche\Edr\Presence\Repository\PresenceRepository;
use AcMarche\Edr\Relation\Utils\OrdreService;
use AcMarche\Edr\Security\Role\EdrSecurityRole;
use AcMarche\Edr\Tuteur\Repository\TuteurRepository;
use AcMarche\Edr\User\Repository\UserRepository;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route(path: '/checkup')]
#[IsGranted(data: 'ROLE_MERCREDI_ADMIN')]
final class CheckupController extends AbstractController
{
    private $tutru = null;

    public function __construct(
        private EnfantRepository $enfantRepository,
        private TuteurRepository $tuteurRepository,
        private UserRepository $userRepository,
        private PresenceRepository $presenceRepository,
        private OrdreService $ordreService,
        private JourRepository $jourRepository,
        private FactureRepository $factureRepository,
        private FactureCalculatorInterface $factureCalculator,
        private FacturePresenceRepository $facturePresenceRepository,
        private PresenceCalculatorInterface $presenceCalculator
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
            if ($user->hasRole(EdrSecurityRole::ROLE_ANIMATEUR) && 0 === \count($user->getAnimateurs())) {
                $bad[] = [
                    'error' => 'Rôle animateur, mais aucun animateur associé',
                    'user' => $user,
                ];
                continue;
            }
            if ($user->hasRole(EdrSecurityRole::ROLE_ECOLE) && 0 === \count($user->getEcoles())) {
                $bad[] = [
                    'error' => 'Rôle école, mais aucune école associée',
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

    #[Route(path: '/presences', name: 'edr_admin_checkup_presence')]
    public function presences(): Response
    {
        $dateTime = new DateTime('01-10-2021');
        $jours = $this->jourRepository->findDaysByMonth($dateTime);
        $presences = $this->presenceRepository->findByDays($jours);
        foreach ($presences as $presence) {
            $ordre = $this->ordreService->getOrdreOnPresence($presence);
            $presence->ordreTmp = $ordre;
            $presence->fratries = $this->ordreService->getFratriesPresents($presence);
        }

        return $this->render(
            '@AcMarcheEdrAdmin/checkup/presences.html.twig',
            [
                'presences' => $presences,
            ]
        );
    }

    #[Route(path: '/factures', name: 'edr_admin_checkup_presence')]
    public function factures(): Response
    {
        $factures = $this->factureRepository->findFacturesByMonth('10-2021');
        $total = 0;
        $data = [];
        $i = 0;
        foreach ($factures as $facture) {
            $tuteur = $facture->getTuteur();
            $facturePresences = $this->facturePresenceRepository->findByFactureAndType(
                $facture,
                FactureInterface::OBJECT_PRESENCE
            );
            foreach ($facturePresences as $presenceFactured) {
                $presence = $this->presenceRepository->find($presenceFactured->getPresenceId());
                if (null !== $presence) {
                    $ordre = $this->presenceCalculator->getOrdreOnPresence($presence);
                    $prix = $this->presenceCalculator->getPrixByOrdre($presence, $ordre);
                }
                $prixFactured = $presenceFactured->getCoutBrut();
                $ordreFactured = $presenceFactured->getOrdre();
                if ($prix !== $prixFactured) {
                    $newcout = 0;
                    $data[$i]['tuteur'] = $tuteur;
                    $data[$i]['facture'] = $facture;
                    $data[$i]['presences'][] = [
                        'object' => $presence,
                        'prix' => 'Passe de '.$prixFactured.' € à '.$prix.' €',
                        'ordre' => 'Passe de '.$ordreFactured.' à '.$ordre,
                    ];
                    if (null !== $presence) {
                        $newcout = $this->presenceCalculator->calculate(
                            $presence
                        );
                    }
                    if (! isset($data[$i]['montant'])) {
                        $data[$i]['montant'] = 0;
                    }
                    $data[$i]['montant'] += ($newcout - $presenceFactured->getCoutCalculated());
                }
            }

            $facture->factureDetailDto = $this->factureCalculator->createDetail($facture);
            $total += $facture->factureDetailDto->total;
            ++$i;
        }

        return $this->render(
            '@AcMarcheEdrAdmin/checkup/factures.html.twig',
            [
                'factures' => $factures,
                'total' => $total,
                'data' => $data,
            ]
        );
    }
}
