<?php

namespace AcMarche\Edr\Controller\Parent;

use AcMarche\Edr\Entity\Tuteur;
use AcMarche\Edr\Tuteur\Utils\TuteurUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Service\Attribute\Required;

trait GetTuteurTrait
{
    private TuteurUtils $tuteurUtils;

    private ?Tuteur $tuteur = null;

    #[Required]
    public function setTuteurUtils(TuteurUtils $tuteurUtils): void
    {
        $this->tuteurUtils = $tuteurUtils;
    }

    public function hasTuteur(): ?Response
    {
        $user = $this->getUser();

        if (! $this->tuteur = $this->tuteurUtils->getTuteurByUser($user)) {
            return $this->redirectToRoute('edr_parent_nouveau');
        }

        return $this->denyAccessUnlessGranted('tuteur_show', $this->tuteur);
    }
}
