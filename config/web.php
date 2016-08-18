<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => [
//        'assetManager'=>[
//            'class'=>'yii\web\AssetManager',
//            'linkAssets'=>true,
//        ],
        'urlManager' => [
            'class'=>'yii\web\UrlManager',
            'enablePrettyUrl' => true,
//            'enableStrictParsing' => true,
//            'showScriptName' => false,
//
            'rules' => [
//                '' => 'site/index',
//                '<action>'=>'site/<action>',
                ['class' => 'yii\rest\UrlRule', 'controller' => ['user','graph','node','edge']],
            ],
        ],
        'request' => [
            'baseUrl' => '',
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '2VsEqwpT-e3-yQlEBjUnwzz9yNNh4_jg',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => false,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
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
        'db' => require(__DIR__ . '/db.php'),
        /*
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        */
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];

    $config['components']['log']['targets'][] = [
        'class' => 'yii\log\FileTarget',
        'levels' => ['info'],
        'categories' => ['DEBUG_INFO'],
        'logFile' => '@app/runtime/logs/API/DEBUG_INFO.log',
        'maxFileSize' => 1024 * 2,
        'maxLogFiles' => 20,
    ];
}

return $config;
