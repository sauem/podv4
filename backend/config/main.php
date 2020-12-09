<?php

use mdm\admin\Module;

require_once(__DIR__ . '/const.php');
$rules = require(__DIR__ . '/rules.php');
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/params.php'
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'layout' => 'main.blade',
    'modules' => [
        'gii' => [
            'class' => \yii\gii\Module::class,
        ],
        'gridview' => [
            'class' => 'kartik\grid\Module',
        ],
        'rbac' => [
            'class' => Module::class,
            'layout' => 'right-menu',
        ],
        'filemanager' => [
            'class' => 'pendalf89\filemanager\Module',
            // Upload routes
            'routes' => [
                // Base absolute path to web directory
                'baseUrl' => '',
                // Base web directory url
                'basePath' => UPLOAD_PATH,
                // Path for uploaded files in web directory
                'uploadPath' => '',
            ],
            // Thumbnails info
            'thumbs' => [
                'small' => [
                    'name' => 'small',
                    'size' => [100, 100],
                ],
                'medium' => [
                    'name' => 'medium',
                    'size' => [300, 200],
                ],
                'large' => [
                    'name' => 'large',
                    'size' => [500, 400],
                ],
            ],
        ],
    ],
    'components' => [
        'authManager' => [
            'class' => 'yii\rbac\DbManager', // or use 'yii\rbac\DbManager'
        ],
        'request' => [
            'csrfParam' => '_csrf-backend',
            'cookieValidationKey' => 'QqhWhK8-MJKqCGbUInK5J2UjWMgFIvWB',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'assetManager' => [
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    'js' => [
                        '/theme/js/jquery.js',
                        '/theme/libs/chart.js/Chart.bundle.min.js'
                    ]
                ],
                'bundles' => [
                    'kartik\form\ActiveFormAsset' => [
                        'bsDependencyEnabled' => false // do not load bootstrap assets for a specific asset bundle
                    ],
                ],
                'yii\bootstrap\BootstrapAsset' => [
                    'sourcePath' => null,
                    'css' => [
                        '/theme/css/bootstrap.min.css',
                       // 'https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css'
                    ],
                    'js' => []
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => $rules,
        ],
    ],
    'as access' => [
        'class' => 'mdm\admin\components\AccessControl',
        'allowActions' => [
            'rbac/*',
        ]
    ],
    'params' => $params,
];
