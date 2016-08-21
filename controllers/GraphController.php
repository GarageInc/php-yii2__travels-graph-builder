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
class GraphController  extends ActiveController
{
    public $modelClass = 'app\models\Graph';

    /**
     * @inheritdoc
     */
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
//
//            'acess' => [
//                'class' => AccessControl::className(),
//                'only' => ['index', 'create', 'update', 'build'],
//                'rules' => [
//                    [
//                        'allow' => true,
//                        'actions' => [],
//                        'roles' => ['@'],
//                    ],
//                    [
//                        'allow' => false,
//                        'actions' => ['index', 'create', 'update', 'build'],
//                        'roles' => ['?'],
//                    ],
//                ],
//            ],
            'corsFilter' => [
                'class' => \yii\filters\Cors::className(),
            ],
        ]);
    }

    public function actions()
    {
        $actions = parent::actions();

        $actions['index']['prepareDataProvider'] = [$this, 'pDPIndex'];
        $actions['index']['prepareDataProvider'] = [$this, 'pDPIndex'];

        return $actions;
    }


    public function getUserId(){

        return $user_id =  Yii::$app->request->post('id', Yii::$app->request->get('id', -1));;
    }

    public function checkAccess($action, $model = null, $params = [])
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

    public function pDPIndex()
    {
        $user_id =  self::getUserId();

        $dataProvider =  Graph::find()->where(['user_id' => $user_id])->all();

        Yii::info( $dataProvider, 'DEBUG_INFO');

        return  $dataProvider;
    }

//    public function actionCreate()
//    {
//        $model = new Graph();
//
//        $userId = $this->getUserId();;
//
//        $model->load(Yii::$app->request->post());
//
//        $model->user_id = $userId;
//
//        return self::isValidGraph($model) && $model->save();
//    }
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
        $graph = $this::findModel($graph_id);

        $nodes = $graph->getNodes()->all();
        $edges = $graph->getEdges()->all();


        $result = [
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

    protected function findModel($id)
    {
        $model = Graph::findOne($id);

        if ( $model!== null && $model->user_id == self::getUserId()) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested graph does not exist.');
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
