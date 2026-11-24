<?php

namespace AcMarche\Edr;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

final class AcMarcheEdrBundle extends AbstractBundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.php');
    }

    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/packages/doctrine.php');
        $container->import('../config/packages/liip_imagine.php');
        $container->import('../config/packages/rate_limiter.php');
        $container->import('../config/packages/security.php');
        $container->import('../config/packages/twig.php');
        $container->import('../config/packages/twig_component.php');
        $container->import('../config/packages/vich_uploader.php');
    }
}
