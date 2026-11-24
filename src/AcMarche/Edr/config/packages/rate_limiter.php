<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('framework', [
        'rate_limiter' => [
            'anonymous_api' => [
                'policy' => 'fixed_window',
                'limit' => 30,
                'interval' => '60 minutes',
            ],
        ],
    ]);
};
