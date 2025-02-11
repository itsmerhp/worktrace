<?php

return [
    'name' => 'WorkTrace',
    //'language' => 'sr',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'assetManager' => [
            'bundles' => [
                // we will use bootstrap css from our theme
                'yii\bootstrap\BootstrapAsset' => [
                    'css' => [], // do not use yii default one
                ],
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'urlManager' => [
            'class' => 'yii\web\UrlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ],
        'session' => [
            'class' => 'yii\web\DbSession',
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/translations',
                    'sourceLanguage' => 'en',
                ],
                'yii' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/translations',
                    'sourceLanguage' => 'en'
                ],
            ],
        ],
        'generallib' => [
            'class' => 'common\components\Generallib',
        ],
        'jwt' => [
            'class' => 'sizeg\jwt\Jwt',
            'key'   => 'WorkTraceJwtSecretKey',
        ],
    ],
    'aliases' => [
        '@uploads' => '@appRoot/uploads',
        '@host' => $_SERVER['SERVER_NAME'] == "localhost" ? "http://localhost/worktrace/" : "http://www.worktrace.co.za/",
        '@themeBase' => '@host/protected/vendor/bower/admin-lte',
        '@backendURL' => '@host/admin',
    ]
];
