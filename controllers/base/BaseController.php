<?php

namespace app\controllers\base;


use app\models\IdentityModel;
use yii\filters\ContentNegotiator;
use yii\filters\Cors;
use app\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\rest\ActiveController;
use yii\web\Controller;
use yii\filters\VerbFilter;

use app\models\ContactForm;
use yii\web\HttpException;
use yii\web\Response;

class BaseController extends Controller
{
    public $enableCsrfValidation = false;

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

    public function getUserId(){

        return $user_id =  Yii::$app->request->post('id', Yii::$app->request->get('id', -1));;
    }

    public function beforeAction($action)
    {
        $user_id =  self::getUserId();
        $pub_token =  Yii::$app->request->post('pub_token', Yii::$app->request->get('pub_token', -1));

        $selectedUser = User::findIdentity( $user_id);

        if( !$selectedUser || !$selectedUser->validateToken($pub_token))
            throw new ForbiddenHttpException();
        return
            true;
    }

    public function convertModelToArray($models, $fields) {

        $result = array();

        foreach ($models as $model) {
            $row = array();

            foreach ($fields as $field){
                $row[$field] = $model->getAttribute( $field);
            }

            array_push($result, $row);
        }

        return $result;
    }
}
