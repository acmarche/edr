<?php

namespace AcMarche\Edr\DependencyInjection;

use AcMarche\Edr\Contrat\Presence\PresenceConstraintInterface;
use Symfony\Component\Config\Builder\ConfigBuilderGenerator;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @see http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
final class AcMarcheEdrExtension extends Extension implements PrependExtensionInterface
{
    private PhpFileLoader $loader;

    public function load(array $configs, ContainerBuilder $containerBuilder): void
    {
        $this->loader->load('services.php');
        $this->loader->load('packages/messenger.php');
        //$this->loader->load('routes/routes.php');

        //auto tag PresenceConstraintInterface
        $containerBuilder->registerForAutoconfiguration(PresenceConstraintInterface::class)
            ->addTag('edr.presence_constraint');
    }

    /**
     * Allow an extension to prepend the extension configurations.
     */
    public function prepend(ContainerBuilder $containerBuilder): void
    {
        $this->loader = $this->initPhpFilerLoader($containerBuilder);

        foreach (array_keys($containerBuilder->getExtensions()) as $name) {
            switch ($name) {
                case 'doctrine':
                    $this->loadConfig('doctrine');

                    break;
                case 'twig':
                    $this->loadConfig('twig');

                    break;
                case 'liip_imagine':
                    $this->loadConfig('liip_imagine');

                    break;
                case 'framework':
                    $this->loadConfig('security');
                    $this->loadConfig('framework');

                    break;
                case 'vich_uploader':
                    $this->loadConfig('vich_uploader');

                    break;
            }
        }
    }

    protected function loadConfig(string $name): void
    {
        $this->loader->load('packages/' . $name . '.php');
    }

    protected function initPhpFilerLoader(ContainerBuilder $containerBuilder): PhpFileLoader
    {
        return new PhpFileLoader(
            $containerBuilder,
            new FileLocator(__DIR__ . '/../../config/'),
            null,
            class_exists(ConfigBuilderGenerator::class) ? new ConfigBuilderGenerator(
                $containerBuilder->getParameter('kernel.cache_dir')
            ) : null
        );
    }
}
