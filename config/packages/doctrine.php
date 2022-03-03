<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension(
        'doctrine',
        [
            'orm' => [
                'mappings' => [
                    'AcMarche\Edr' => [
                        'is_bundle' => false,
                        'dir' => '%kernel.project_dir%/src/AcMarche/Edr/src/Entity',
                        'prefix' => 'AcMarche\Edr',
                        'alias' => 'AcMarche\Edr',
                    ],
                ],
            ],
        ]
    );
};
