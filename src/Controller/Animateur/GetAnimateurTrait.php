<?php

namespace AcMarche\Edr\Controller\Animateur;

use AcMarche\Edr\Ecole\Utils\EcoleUtils;
use AcMarche\Edr\Entity\Animateur;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Service\Attribute\Required;

trait GetAnimateurTrait
{
    private ?Animateur $animateur = null;

    private EcoleUtils $ecoleUtils;

    #[Required]
    public function setEcoleUtils(EcoleUtils $ecoleUtils): void
    {
        $this->ecoleUtils = $ecoleUtils;
    }

    public function hasAnimateur(): ?Response
    {
        $user = $this->getUser();
        $this->animateur = $user->getAnimateur();

        if (!$this->animateur) {
            return $this->redirectToRoute('edr_animateur_nouveau');
        }

        return $this->denyAccessUnlessGranted('animateur_index', null);
    }
}
