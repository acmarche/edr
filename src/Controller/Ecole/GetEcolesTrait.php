<?php

namespace AcMarche\Edr\Controller\Ecole;

use AcMarche\Edr\Ecole\Utils\EcoleUtils;
use AcMarche\Edr\Entity\Scolaire\Ecole;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Service\Attribute\Required;

trait GetEcolesTrait
{
    private EcoleUtils $ecoleUtils;

    /**
     * @var Ecole[]
     */
    private iterable  $ecoles;

    #[Required]
    public function setEcoleUtils(EcoleUtils $ecoleUtils): void
    {
        $this->ecoleUtils = $ecoleUtils;
    }

    public function hasEcoles(): ?Response
    {
        $user = $this->getUser();
        $this->ecoles = $this->ecoleUtils->getEcolesByUser($user);

        if (! $this->ecoles) {
            return $this->redirectToRoute('edr_ecole_nouveau');
        }

        return $this->denyAccessUnlessGranted('ecole_index', null);
    }
}
