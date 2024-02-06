<?php

use Symfony\Config\TwigConfig;

return static function (TwigConfig $twigConfig): void {
    $twigConfig
        ->formThemes(['bootstrap_5_layout.html.twig'])
        ->path('%kernel.project_dir%/src/AcMarche/Edr/templates/admin', 'AcMarcheEdrAdmin')
        ->path('%kernel.project_dir%/src/AcMarche/Edr/templates/parent', 'AcMarcheEdrParent')
        ->path('%kernel.project_dir%/src/AcMarche/Edr/templates/ecole', 'AcMarcheEdrEcole')
        ->path('%kernel.project_dir%/src/AcMarche/Edr/templates/animateur', 'AcMarcheEdrAnimateur')
        ->path('%kernel.project_dir%/src/AcMarche/Edr/templates/email', 'AcMarcheEdrEmail')
        ->global('bootcdn', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css')
        ->global('edr_register', '%env(EDR_REGISTER)%')
        ->global('edr_accueil', '%env(EDR_ACCUEIL)%')
        ->global('edr_paiement', '%env(EDR_PAIEMENT)%')
        ->global('presence_nb_days', '%env(EDR_PRESENCE_DEADLINE_DAYS)%')
        ->global('pedagogique_nb_days', '%env(EDR_PEDAGOGIQUE_DEADLINE_DAYS)%');
};
