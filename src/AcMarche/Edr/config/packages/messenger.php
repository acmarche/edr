<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Mailer\Messenger\SendEmailMessage;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('framework', [
        'messenger' => [
            'transports' => [
                'async' => [
                    'dsn' => '%env(MESSENGER_TRANSPORT_DSN)%',
                    'options' => [
                        'auto_setup' => true,
                        'use_notify' => true,
                        //PostgreSQL’s
                        'check_delayed_interval' => 60000,
                    ],
                    'retry_strategy' => [
                        'max_retries' => 3,
                        'multiplier' => 2,
                    ],
                ],
            ],
            'routing' => [
                SendEmailMessage::class => ['async'],
            ],
        ],
    ]);
};
