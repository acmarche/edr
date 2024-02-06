<?php

namespace AcMarche\Edr\Sante\Utils;

use AcMarche\Edr\Entity\Sante\SanteFiche;
use AcMarche\Edr\Entity\Sante\SanteQuestion;
use AcMarche\Edr\Entity\Sante\SanteReponse;
use AcMarche\Edr\Sante\Repository\SanteQuestionRepository;
use AcMarche\Edr\Sante\Repository\SanteReponseRepository;

final readonly class SanteBinder
{
    public function __construct(
        private SanteQuestionRepository $santeQuestionRepository,
        private SanteReponseRepository $santeReponseRepository
    ) {
    }

    /**
     * @return SanteQuestion[]
     */
    public function bindResponses(SanteFiche $santeFiche): array
    {
        $questions = $this->santeQuestionRepository->findAllOrberByPosition();
        if (!$santeFiche->getId()) {
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
