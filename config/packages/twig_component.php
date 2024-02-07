<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('twig_component', [
        'anonymous_template_directory' => 'components/',
        'defaults' => [
            'AcMarche\\Edr\\Twig\\' => 'components/',
        ],
    ]);
};
