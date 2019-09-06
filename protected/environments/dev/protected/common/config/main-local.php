<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=192.168.1.240;dbname=crityk',
            'username' => 'root',
            'password' => 'inxdb#2015',
            'charset' => 'utf8mb4',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => false,
            'transport'        => [
                'class'      => 'Swift_SmtpTransport',
                'host'       => 'host153.hostmonster.com',
                'username'   => 'cmsadmin@inheritx.com',
                'password'   => 'cmsadmin@inx',
                'port'       => '465',
                'encryption' => 'ssl',
            ],
        ],
    ],
];
