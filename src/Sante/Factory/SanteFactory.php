<?php

namespace AcMarche\Edr\Sante\Factory;

use AcMarche\Edr\Entity\Enfant;
use AcMarche\Edr\Entity\Sante\SanteFiche;
use AcMarche\Edr\Entity\Sante\SanteQuestion;
use AcMarche\Edr\Entity\Sante\SanteReponse;
use AcMarche\Edr\Sante\Repository\SanteFicheRepository;
use AcMarche\Edr\Sante\Repository\SanteReponseRepository;

final class SanteFactory
{
    public function __construct(
        private SanteFicheRepository $santeFicheRepository,
        private SanteReponseRepository $santeReponseRepository
    ) {
    }

    public function getSanteFicheByEnfant(Enfant $enfant): SanteFiche
    {
        if (null === ($santeFiche = $this->santeFicheRepository->findOneBy([
            'enfant' => $enfant,
        ]))) {
            $santeFiche = new SanteFiche($enfant);
            $this->santeFicheRepository->persist($santeFiche);
        }

        return $santeFiche;
    }

    public function createSanteReponse(SanteFiche $santeFiche, SanteQuestion $santeQuestion): SanteReponse
    {
        $santeReponse = new SanteReponse($santeFiche, $santeQuestion);
        $this->santeReponseRepository->persist($santeReponse);

        return $santeReponse;
    }
}
