<?php

namespace app\controllers;
use app\models\IdentityModel;
use yii\filters\Cors;
use Yii;
use yii\filters\AccessControl;
use yii\rest\ActiveController;
use yii\web\Controller;
use yii\filters\VerbFilter;

use app\models\ContactForm;

class SiteController extends Controller
{

    public $username;
    public $password;
    public $rememberMe = true;

    private $_user = false;

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
                    'Access-Control-Allow-Credentials' => null,
                    'Access-Control-Max-Age' => 86400,
                    'Access-Control-Expose-Headers' => [],
                ],
            ],
        ];

        return $behaviors;
    }


    /**
     * @inheritdoc
     */
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
//
//    /**
//     * Displays homepage.
//     *
//     * @return string
//     */
//    public function actionIndex()
//    {
//
//        if ( Yii::$app->user->isGuest) {
//            return $this::actionLogin();
//        }
//
//        return "blablabla";
//    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        $model = new IdentityModel();

        $token = "blablabla";

        if ($model->load(Yii::$app->request->post())) {

            $token = $model->login();
        }

        return Yii::$app->user->isGuest;

        return $token;
    }
//
//    /**
//     * Logout action.
//     *
//     * @return string
//     */
//    public function actionLogout()
//    {
//        Yii::$app->user->logout();
//
//        return $this->goHome();
//    }


    public function actionIndex()
    {
        return $this->renderContent(null);
    }
}
