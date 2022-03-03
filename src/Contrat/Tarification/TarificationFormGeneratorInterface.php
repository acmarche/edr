<?php

namespace AcMarche\Edr\Contrat\Tarification;

use AcMarche\Edr\Entity\Jour;
use Symfony\Component\Form\FormInterface;

interface TarificationFormGeneratorInterface
{
    public function generateForm(Jour $jour): FormInterface;

    public function generateTarifsHtml(Jour $jour): string;
}
