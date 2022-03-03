<?php

namespace AcMarche\Edr\Facture\Factory;

use AcMarche\Edr\Entity\Plaine\Plaine;
use AcMarche\Edr\Facture\FactureInterface;
use AcMarche\Edr\Facture\Repository\FactureRepository;

class CommunicationFactoryHotton implements CommunicationFactoryInterface
{
    public function __construct(
        private FactureRepository $factureRepository
    ) {
    }

    /**
     * Pour la communication à faire apparaitre :.
     *
     * Raccourci de l’école + n° facture + année (année, non pas d’émission de la facture, mais du mois de garderie)
     *
     * Exemple : COM HAMP 1/2021
     *
     * Raccourcis des écoles :
     *
     * Communale de Hampteau : COMHAM
     * Communale de Hotton : COMHOT
     * Libre Bourdon : LB
     * Libre Hotton : LH
     * Libre Melreux : LM
     * Enrico Macias : EM
     *
     * La structure imposée par le standard bancaire belge est la suivante :
     *
     * +++ 000 / 0000 / 000XX +++
     *
     * Les deux derniers numéros « XX » étant des numéros de contrôle.
     *
     * Afin de faciliter la lecture de ce numéro, Smoall le compose de la manière suivante :
     *
     * +++ YYY / YNNN / NNNXX +++
     * YYYY = L’année de la date de facture
     * NNNNNN = Le numéro de la facture.
     *
     * Exemple : La communication structurée suivante +++ 201 / 8000 / 53522 +++ se rapporte donc à la facture n° 535 de l’année 2018.
     */
    public function generateForPresence(FactureInterface $facture): string
    {
        $ecoles = '';
        foreach ($facture->ecolesListing as $ecole) {
            if ($ecole->getAbreviation()) {
                $ecoles .= $ecole->getAbreviation().' ';
            } else {
                $ecoles .= $ecole->getLocalite();
            }
        }

        //list($month, $year) = explode('-', $facture->getMois());
        $communication = $ecoles.' '.$facture->getId().' '.$facture->getMois();

        return $communication;
    }

    public function generateForPlaine(Plaine $plaine, FactureInterface $facture): string
    {
        $communication = $plaine->getCommunication();
        if (! $communication) {
            return $plaine->getSlug().' '.$facture->getId();
        }

        return $communication.' '.$facture->getId();
    }
}
