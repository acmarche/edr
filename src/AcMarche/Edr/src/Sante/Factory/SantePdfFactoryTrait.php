<?php

namespace AcMarche\Edr\Sante\Factory;

use AcMarche\Edr\Entity\Sante\SanteFiche;
use AcMarche\Edr\Organisation\Repository\OrganisationRepository;
use AcMarche\Edr\Pdf\PdfDownloaderTrait;
use AcMarche\Edr\Sante\Repository\SanteQuestionRepository;
use AcMarche\Edr\Sante\Utils\SanteChecker;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class SantePdfFactoryTrait
{
    use PdfDownloaderTrait;

    public function __construct(
        private readonly SanteQuestionRepository $santeQuestionRepository,
        private readonly OrganisationRepository $organisationRepository,
        private readonly SanteChecker $santeChecker,
        private readonly Environment $environment
    ) {
    }

    public function santeFiche(SanteFiche $santeFiche): Response
    {
        $isComplete = $this->santeChecker->isComplete($santeFiche);
        $questions = $this->santeQuestionRepository->findAllOrberByPosition();
        $organisation = $this->organisationRepository->getOrganisation();
        $enfant = $santeFiche->getEnfant();
        $html = $this->environment->render(
            '@AcMarcheEdr/sante/pdf/fiche.html.twig',
            [
                'enfant' => $enfant,
                'sante_fiche' => $santeFiche,
                'is_complete' => $isComplete,
                'questions' => $questions,
                'organisation' => $organisation,
            ]
        );

        // return new Response($html);
        return $this->downloadPdf($html, $enfant->getSlug() . '-sante.pdf');
    }
}
