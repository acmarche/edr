<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension(
        'twig',
        [
            'form_themes' => ['bootstrap_5_layout.html.twig'],
            'paths' => [
                '%kernel.project_dir%/src/AcMarche/Edr/templates/admin' => 'AcMarcheEdrAdmin',
                '%kernel.project_dir%/src/AcMarche/Edr/templates/parent' => 'AcMarcheEdrParent',
                '%kernel.project_dir%/src/AcMarche/Edr/templates/ecole' => 'AcMarcheEdrEcole',
                '%kernel.project_dir%/src/AcMarche/Edr/templates/animateur' => 'AcMarcheEdrAnimateur',
                '%kernel.project_dir%/src/AcMarche/Edr/templates/email' => 'AcMarcheEdrEmail',
            ],
            'globals' => [
                'edr_register' => '%env(EDR_REGISTER)%',
                'edr_accueil' => '%env(EDR_ACCUEIL)%',
                'edr_paiement' => '%env(EDR_PAIEMENT)%',
                'presence_nb_days' => '%env(EDR_PRESENCE_DEADLINE_DAYS)%',
                'pedagogique_nb_days' => '%env(EDR_PEDAGOGIQUE_DEADLINE_DAYS)%',
                'bootcdn' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css',
            ],
        ]
    );
};
