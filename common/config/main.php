<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'language' => 'en',
    'timeZone' => 'Asia/Ho_Chi_Minh',
    'components' => [
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/messages',
                ],
            ],
        ],
        'cache' => [
            'class' => 'yii\redis\Cache',
            #'class' => 'yii\caching\FileCache',
            'keyPrefix' => 'tcom_'
        ],
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => REDIS_HOST,
            'port' => REDIS_PORT,
            'password' => REDIS_PASS,
            'database' => 0,
            'retries' => 1,
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => SQL_HOST,
            'username' => SQL_USER_NAME,
            'password' => SQL_PASSWORD,
            'charset' => 'utf8',
            'enableSchemaCache' => true,
            // Duration of schema cache.
            'schemaCacheDuration' => 3600,
            // Name of the cache component used to store schema information
            'schemaCache' => 'cache',
            'queryCache' => 'cache',
            'enableQueryCache' => true,
        ],
        'view' => [
            'class' => 'yii\web\View',
            'renderers' => [
                'blade' => [
                    'class' => '\cyneek\yii2\blade\ViewRenderer',
                    'cachePath' => '@runtime/blade_cache',
                ],
            ],
        ],
    ],
];
