<?php
$conf = [
    'debugMode' => false,
    'logLevel' => 100, //for prod set 200
    'timeZone' => date_default_timezone_get(),

    'rootDir' => '/Applications/MAMP/htdocs/NISH/nish2-sample',
    'sampleAppBasePath' => '/NISH/nish2-sample'
];

$conf['sampleAppRootDir'] = $conf['rootDir'].'/apps/Sample';

return array_merge($conf, [
    'cacheSystem' => null, //file or redis

    'mysql' => [
        'driver' => 'pdo_mysql',
        'host' => 'localhost',
        'user' => 'root',
        'password' => 'root',
        'dbname' => 'nish2_sample'
    ],

    'redis' => [
        'dsn' => 'redis://localhost:6379',
        'options' => [
            'compression' => false,
            'lazy' => false,
            'persistent' => 0,
            'persistent_id' => null,
            'tcp_keepalive' => 0,
            'timeout' => 30,
            'read_timeout' => 0,
            'retry_interval' => 0
        ]
    ],

    'availableLanguages' => [
        'en' => 'İngilizce',
        'tr' => 'Türkçe'
    ],

    'defaultLang' => 'tr',

    'sampleAppStylesVersion' => '1',

    'mailer' => \Nish\Utils\Mailer\FileMailer::class,

    'mailReceivers' => ['testreceiver@localhost'],

    'mailSender' => [
        'host' => 'localhost',
        'smtpSecure' => 'tls',
        'port' => 587,
        'username' => 'testsender@localhost',
        'password' => 'pwd',
        'from' => 'testsender@localhost',
        'replyTo' => 'testsender@localhost'
    ]
]);