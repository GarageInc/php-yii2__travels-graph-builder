<?php

namespace app\controllers;

use app\models\Node;
use app\models\User;
use app\pathfinders\DijkstraAlgorithm;
use Yii;
use app\models\Graph;
use yii\data\ActiveDataProvider;
use yii\rest\ActiveController;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Response;
/**
 * GraphController implements the CRUD actions for Graph model.
 */
class GraphController  extends Controller
{
    public $enableCsrfValidation = false;
//    public $modelClass = 'app\models\Graph';

    public function behaviors()
    {

        return
            \yii\helpers\ArrayHelper::merge(parent::behaviors(),  [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'corsFilter' => [
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
            ]
        ]);
    }

//    public function actions()
//    {
//        $actions = parent::actions();
//
//        $actions['index']['prepareDataProvider'] = [$this, 'pDPIndex'];
//
//        return $actions;
//    }


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

    // prepareDataProviders

    public function actionIndex()
    {
        $user_id =  self::getUserId();

        $dataProvider =  Graph::find()->where(['user_id' => $user_id])->all();

        $models = self::convertModelToArray($dataProvider, ["user_id", "graphname", "id"]);

        return  json_encode($models);
    }

    public function actionCreate()
    {
        $model = new Graph();

        $userId = $this->getUserId();;

        $params = [
            "user_id" => $userId,
            "graphname" => Yii::$app->request->post()["graphname"],
        ];

        $model->setAttributes( $params);

        $result = self::isValidGraph($model) && $model->save();

        return json_encode($result);
    }
//
//    public function actionUpdate($id)
//    {
//        $model = $this->findModel($id);
//
//        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//            return $this->redirect(['view', 'id' => $model->id]);
//        } else {
//            return  $model;
//        }
//    }

    public function actionSave()
    {
        $graph_id = Yii::$app->request->post()["graph_id"];
        $graphname = Yii::$app->request->post()["graphname"];

        $model = $this->findModel( $graph_id);

        $model->graphname = $graphname;

        return $model->save();
    }


    public function actionBuild( $id)
    {
        $model = $this->findModel( $id);

        return [
            'graph' => $model,
            'nodes' => $model->getNodes()->all()
        ];
    }

    public function actionDelete($id)
    {
        self::findModel($id)->delete();

        return $this->redirect(['index']);
    }


    public function actionItem( $graph_id, $node_first_id, $node_second_id)
    {
        $result = array(
            '$graph_id'  => $graph_id,
            '$node_first_id' => $node_first_id,
            '$node_second_id' => $node_second_id
        );

        return $result;
    }


    public function actionStructure( $graph_id )
    {
        $graph = $this::findModel( $graph_id);

        $nodes = $graph->getNodes()->all();
        $edges = $graph->getEdges()->all();

        $result = [
            "id" => $graph_id,
            "graphname" => $graph->graphname,
            "nodes" => $this::convertModelToArray($nodes, ["id", "nodename"]),
            "edges" => $this::convertModelToArray($edges, ["id", 'node_first_id', 'node_second_id', 'weight'])
        ];

        return json_encode($result);
    }


    public function actionFindPath( $graph_id, $node_first_id, $node_second_id)
    {
        $graph = $this::findModel( $graph_id);

        $edges = $this::convertModelToArray( $graph->getEdges()->all(), ["node_first_id", "node_second_id", "weight" ]);

        $dist_array = array();

        foreach ($edges as $edge){
            $dist_array[$edge["node_first_id"]][$edge["node_second_id"]] = $edge["weight"];
            $dist_array[$edge["node_second_id"]][$edge["node_first_id"]] = $edge["weight"];
        }

        $result = DijkstraAlgorithm::findPath($dist_array,$node_first_id,$node_second_id);

        return json_encode($result);
    }

    // UTILS

    public static function isValidGraph($graph){

        if($graph->user_id && $graph->graphname){
            return true;
        }

        return false;
    }

    protected function findModel( $id)
    {
        $model = Graph::findOne( $id);

        if ( $model!== null && $model->user_id == self::getUserId()) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested graph does not exist by id = ' . $id);
        }
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
