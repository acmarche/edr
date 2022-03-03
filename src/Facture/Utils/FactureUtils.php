<?php

namespace AcMarche\Edr\Facture\Utils;

use AcMarche\Edr\Contrat\Facture\FactureCalculatorInterface;
use AcMarche\Edr\Entity\Enfant;
use AcMarche\Edr\Entity\Facture\Facture;
use Symfony\Component\String\Slugger\SluggerInterface;

class FactureUtils
{
    public function __construct(
        public SluggerInterface $slugger,
        private FactureCalculatorInterface $factureCalculator
    ) {
    }

    /**
     * @return array|Enfant[]
     */
    public function getEnfants(Facture $facture): array
    {
        $enfants = [];
        foreach ($facture->getFacturePresences() as $facturePresence) {
            $enfant = $facturePresence->getNom().' '.$facturePresence->getPrenom();
            $slug = $this->slugger->slug($enfant);
            $enfants[$slug->toString()] = $enfant;
        }

        return $enfants;
    }

    /**
     * @param Facture[] $factures
     * @return array|Facture[]
     */
    public function groupByTuteur(iterable $factures): array
    {
        $data = [];
        foreach ($factures as $facture) {
            $facture->factureDetailDto = $this->factureCalculator->createDetail($facture);
            $tuteur = $facture->getTuteur();
            $data[$tuteur->getId()]['tuteur'] = $tuteur;
            $data[$tuteur->getId()]['factures'][] = $facture;
            if(!isset($data[$tuteur->getId()]['total'])){
                $data[$tuteur->getId()]['total'] = $facture->factureDetailDto->total;
            }
            $data[$tuteur->getId()]['total'] += $facture->factureDetailDto->total;
        }

        return $data;
    }
}
