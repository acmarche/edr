<?php

use AcMarche\Edr\Namer\DirectoryNamer;
use AcMarche\Edr\Parameter\Option;
use AcMarche\Edr\Security\Ldap\LdapEdr;
use AcMarche\Edr\ServiceIterator\AfterUserRegistration;
use AcMarche\Edr\ServiceIterator\Register;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Ldap\Adapter\ExtLdap\Adapter;
use Symfony\Component\Ldap\Ldap;
use Symfony\Component\Ldap\LdapInterface;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::REGISTER, (bool)'%env(MERCREDI_REGISTER)%');
    $parameters->set(Option::LDAP_DN, '%env(ACLDAP_DN)%');
    $parameters->set(Option::LDAP_USER, '%env(ACLDAP_USER)%');
    $parameters->set(Option::LDAP_PASSWORD, '%env(ACLDAP_PASSWORD)%');

    $services = $containerConfigurator->services();

    $services
        ->defaults()
        ->autowire()
        ->autoconfigure()
        ->private();

    $services->load('AcMarche\Edr\\', __DIR__.'/../src/*')
        ->exclude([__DIR__.'/../src/{Entity,Tests2}']);

    $services->set(DirectoryNamer::class)
        ->public();

    $services->instanceof(AfterUserRegistration::class)
        ->tag('app.user.after_registration');

    $services->set(Register::class)
        ->arg('$secondaryFlows', tagged_iterator('app.user.after_registration'));

    if (interface_exists(LdapInterface::class)) {
        $services
            ->set(Ldap::class)
            ->args([service(Adapter::class)])
            ->tag('ldap');
        $services->set(Adapter::class)
            ->args(
                [
                    [
                        'host' => '%env(ACLDAP_URL)%',
                        'port' => 636,
                        'encryption' => 'ssl',
                        'options' => [
                            'protocol_version' => 3,
                            'referrals' => false,
                        ],
                    ],
                ]
            );

        $services->set(LdapEdr::class)
            ->arg('$adapter', service(Adapter::class))
            ->tag('ldap'); //necessary for new LdapBadge(LdapEdr::class)
    }
};
