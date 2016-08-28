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
use app\controllers\base\BaseController;

/**
 * GraphController implements the CRUD actions for Graph model.
 */
class GraphController  extends BaseController
{


//    public function actions()
//    {
//        $actions = parent::actions();
//
//        $actions['index']['prepareDataProvider'] = [$this, 'pDPIndex'];
//
//        return $actions;
//    }


    public function beforeAction($action)
    {
        $isCheckGuest = $action->id!="index" && $action->id!="structure" && $action->id!="findpath";

        if( $isCheckGuest){
            $user_id =  self::getUserId();
            $pub_token =  Yii::$app->request->post('pub_token', Yii::$app->request->get('pub_token', -1));

            $selectedUser = User::findIdentity( $user_id);

            if( !$selectedUser || !$selectedUser->validateToken($pub_token))
                throw new ForbiddenHttpException();
        }

        $isCheckGraph = $action->id!="index" && $action->id!="usergraphs" && $action->id != "create" && $action->id != "structure" && $action->id != "findpath";

        if( $isCheckGraph ){

            $graph_id =  Yii::$app->request->post('graph_id', Yii::$app->request->get('graph_id', -1));

            $graph = Graph::findOne( $graph_id);

            if( !$graph || $graph->user_id != self::getUserId())
                throw new ForbiddenHttpException();
            // pass
        }// pass

        return true;
    }

    // prepareDataProviders

    public function actionIndex()
    {
        $dataProvider =  Graph::find()->all();

        $models = self::convertModelToArray($dataProvider, ["user_id", "graphname", "id"]);

        return  json_encode($models);
    }

    public function actionUsergraphs()
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

    public function actionSave()
    {
        $graph_id = Yii::$app->request->post()["graph_id"];
        $graphname = Yii::$app->request->post()["graphname"];

        $model = $this->findModel( $graph_id);

        $model->graphname = $graphname;

        return $model->save();
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
            "user_id" => $graph->user_id,
            "graphname" => $graph->graphname,
            "nodes" => $this::convertModelToArray($nodes, ["id", "nodename"]),
            "edges" => $this::convertModelToArray($edges, ["id", 'node_first_id', 'node_second_id', 'weight'])
        ];

        return json_encode($result);
    }


    public function actionFindpath()
    {
        $graph_id = Yii::$app->request->post()["graph_id"];
        $node_first_id = Yii::$app->request->post()["node_first_id"];
        $node_second_id = Yii::$app->request->post()["node_second_id"];

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

        if ( $model!== null ) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested graph does not exist by id = ' . $id);
        }
    }

}
