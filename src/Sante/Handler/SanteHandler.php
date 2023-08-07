<?php

namespace AcMarche\Edr\Sante\Handler;

use AcMarche\Edr\Entity\Sante\SanteReponse;
use AcMarche\Edr\Entity\Enfant;
use AcMarche\Edr\Entity\Sante\SanteFiche;
use AcMarche\Edr\Entity\Sante\SanteQuestion;
use AcMarche\Edr\Sante\Factory\SanteFactory;
use AcMarche\Edr\Sante\Repository\SanteFicheRepository;
use AcMarche\Edr\Sante\Repository\SanteReponseRepository;
use AcMarche\Edr\Sante\Utils\SanteBinder;
use Doctrine\Common\Collections\Collection;

final class SanteHandler
{
    public function __construct(
        private readonly SanteFicheRepository $santeFicheRepository,
        private readonly SanteReponseRepository $santeReponseRepository,
        private readonly SanteFactory $santeFactory,
        private readonly SanteBinder $santeBinder
    ) {
    }

    public function init(Enfant $enfant, bool $bind = true): SanteFiche
    {
        $santeFiche = $this->santeFactory->getSanteFicheByEnfant($enfant);
        if ($bind) {
            $this->santeBinder->bindResponses($santeFiche);
        }

        return $santeFiche;
    }

    /**
     * Si pas de reponse ou remarque on ne cree pas la reponse.
     *
     * @param SanteQuestion[]|Collection $questions
     *
     * @return void|null
     */
    public function handle(SanteFiche $santeFiche, array|Collection $questions): void
    {
        $this->santeFicheRepository->flush();
        foreach ($questions as $question) {
            if (null === $question->getReponseTxt()) {
                return;
            }

            if (!($reponse = $this->santeReponseRepository->getResponse($santeFiche, $question)) instanceof SanteReponse) {
                $reponse = $this->santeFactory->createSanteReponse($santeFiche, $question);
            }

            $reponse->setReponse($question->getReponseTxt());
            $reponse->setRemarque($question->getRemarque());
            $this->santeReponseRepository->flush();
        }
    }
}
