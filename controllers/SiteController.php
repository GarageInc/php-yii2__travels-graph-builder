<?php

namespace app\controllers;

include('../crypt/RSA.php');

use app\models\IdentityModel;
use Crypt_RSA;
use yii\filters\ContentNegotiator;
use yii\filters\Cors;
use Yii;
use yii\filters\AccessControl;
use yii\rest\ActiveController;
use yii\web\Controller;
use yii\filters\VerbFilter;

use app\models\ContactForm;
use yii\web\HttpException;
use yii\web\Response;

class SiteController extends Controller
{
    public $enableCsrfValidation = false;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::className(),
            'cors' => [],
            'actions' => [
                'login' => [
                    'Origin' => ['*'],
                    'Access-Control-Allow-Origin' => ['*'],
                    'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                    'Access-Control-Request-Headers' => ['*'],
                    'Access-Control-Allow-Credentials' => true,
                    'Access-Control-Max-Age' => 86400,
                    'Access-Control-Expose-Headers' => [],
                ],
            ],
        ];

        return $behaviors;
    }
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionLogin()
    {
//        $opensslConfigPath = "C:/data/php.sudo-rm-rf.ru/openssl.cnf";

//        $config = array(
////            "config" => $opensslConfigPath,
//            "digest_alg" => "sha512",
//            "private_key_bits" => 4096,
//            "private_key_type" => OPENSSL_KEYTYPE_RSA,
//        );
//
//        $res = openssl_pkey_new($config);
//
////        $res =  openssl_error_string ( );
////        return json_encode($res);
//
//        openssl_pkey_export($res, $privKey);
//
//        $pubKey = openssl_pkey_get_details($res);
//        $pubKey = $pubKey["key"];
//
//        $data = 'plaintext data goes here';
//
//        openssl_public_encrypt($data, $encrypted, $pubKey);
//
//        openssl_private_decrypt($encrypted, $decrypted, $privKey);

//        $rsa = new Crypt_RSA();
//
//        extract($rsa->createKey());
//
//        $privatekey = $rsa->getPrivateKey();
//        $publickey = $rsa->getPublicKey();
//
//        $encrypted =  $rsa->encrypt("plaintext data goes here");
//        $decrypted =  $rsa->decrypt($encrypted);
//
//        return json_encode([
//            "data" => "plaintext data goes here",
////            "pub_key" => $publickey,
////            "priv_key" => $privatekey,
//            "encrypted" => $encrypted,
//            "decrypted" => $decrypted
//        ]);


        $model = new IdentityModel();

        $params = [
            "password" => Yii::$app->request->post()["password"],
            "username" => Yii::$app->request->post()["username"],
            "rememberMe" => Yii::$app->request->post()["rememberMe"] == "true" ? true : false,
        ];

        $model->setAttributes($params);

        $params = $model->login();

        // каша, пересмотреть
        if( $params ){
            return json_encode( $params);
        } else {
            throw new HttpException("401");
        }
    }
}
