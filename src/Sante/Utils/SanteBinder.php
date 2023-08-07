<?php

namespace AcMarche\Edr\Sante\Utils;

use AcMarche\Edr\Entity\Sante\SanteReponse;
use AcMarche\Edr\Entity\Sante\SanteFiche;
use AcMarche\Edr\Entity\Sante\SanteQuestion;
use AcMarche\Edr\Sante\Repository\SanteQuestionRepository;
use AcMarche\Edr\Sante\Repository\SanteReponseRepository;

final class SanteBinder
{
    public function __construct(
        private readonly SanteQuestionRepository $santeQuestionRepository,
        private readonly SanteReponseRepository $santeReponseRepository
    ) {
    }

    /**
     * @return SanteQuestion[]
     */
    public function bindResponses(SanteFiche $santeFiche): array
    {
        $questions = $this->santeQuestionRepository->findAllOrberByPosition();
        if (! $santeFiche->getId()) {
            $santeFiche->setQuestions($questions);

            return $questions;
        }

        foreach ($questions as $question) {
            $question->setReponseTxt(null);
            if (($reponse = $this->santeReponseRepository->getResponse($santeFiche, $question)) instanceof SanteReponse) {
                $question->setReponseTxt($reponse->getReponse());

                $question->setRemarque($reponse->getRemarque());
            }
        }

        $santeFiche->setQuestions($questions);

        return $questions;
    }
}
