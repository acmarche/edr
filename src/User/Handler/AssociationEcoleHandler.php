<?php

namespace AcMarche\Edr\User\Handler;

use AcMarche\Edr\Ecole\Repository\EcoleRepository;
use AcMarche\Edr\Entity\Scolaire\Ecole;
use AcMarche\Edr\Entity\Security\User;
use AcMarche\Edr\User\Dto\AssociateUserEcoleDto;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

final class AssociationEcoleHandler
{
    private readonly FlashBagInterface $flashBag;

    public function __construct(
        private readonly EcoleRepository $ecoleRepository,
        RequestStack $requestStack
    ) {
        $this->flashBag = $requestStack->getSession()->getFlashBag();
    }

    public function handleAssociateEcole(AssociateUserEcoleDto $associateUserEcoleDto): void
    {
        $ecoles = $associateUserEcoleDto->getEcoles();
        $user = $associateUserEcoleDto->getUser();

        if ([] !== $this->ecoleRepository->getEcolesByUser($user)) {
            //remove old ecoles
            $user->getEcoles()->clear();
        }

        foreach ($ecoles as $ecole) {
            $user->addEcole($ecole);
        }

        $this->ecoleRepository->flush();

        $this->flashBag->add('success', 'L\'utilisateur a bien été associé.');
    }

    public function handleDissociateEcole(User $user, Ecole $ecole): Ecole
    {
        $user->removeEcole($ecole);

        $this->ecoleRepository->flush();
        $this->flashBag->add('success', 'L\'école a bien été dissociée.');

        return $ecole;
    }
}
