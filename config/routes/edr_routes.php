<?php

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routingConfigurator): void {
    $routingConfigurator
        ->import('../../src/AcMarche/Edr/src/Controller/Front/');

    $routingConfigurator
        ->import('../../src/AcMarche/Edr/src/Controller/Admin/')
        ->prefix('admin/');

    $routingConfigurator
        ->import('../../src/AcMarche/Edr/src/Controller/Parent/')
        ->prefix('parent/');

    $routingConfigurator
        ->import('../../src/AcMarche/Edr/src/Controller/Ecole/')
        ->prefix('ecole/');
};
