<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('twig', [
        'form_themes' => ['bootstrap_5_layout.html.twig', '@AcMarcheEdr/commun/form/ckeditor5.html.twig'],
        'paths' => [
            '%kernel.project_dir%/src/AcMarche/Edr/templates/admin' => 'AcMarcheEdrAdmin',
            '%kernel.project_dir%/src/AcMarche/Edr/templates/parent' => 'AcMarcheEdrParent',
            '%kernel.project_dir%/src/AcMarche/Edr/templates/email' => 'AcMarcheEdrEmail',
        ],
        'globals' => [
            'bootcdn' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css',
        ],
    ]);
};
