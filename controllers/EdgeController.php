<?php

namespace app\controllers;

use app\models\Graph;
use Yii;
use app\models\Edge;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\controllers\base\BaseController;

class EdgeController extends BaseController
{



    public function actionCreate()
    {
        $model = new Edge();

        $model->load(Yii::$app->request->post());

        $model->graph_id = Yii::$app->request->post("graph_id");
        $model->weight = Yii::$app->request->post("weight");
        $model->node_first_id = Yii::$app->request->post("node_first_id");
        $model->node_second_id = Yii::$app->request->post("node_second_id");

        if( $model->node_first_id == $model->node_second_id){
            throw new Exception("Error with unhandled edge!");
        }

        if( $model->save())
            return json_encode([
                'id' => $model->id,
                'weight' => $model->weight,
                'node_first_id' => $model->node_first_id,
                'node_second_id' => $model->node_second_id
            ]);
        else
            throw new Exception();
    }

    public function beforeAction($action)
    {
        parent::beforeAction($action);

        $graph_id =  Yii::$app->request->post('graph_id', Yii::$app->request->get('graph_id', -1));

        $graph = Graph::findOne( $graph_id);

        if( !$graph || $graph->user_id != self::getUserId())
            throw new ForbiddenHttpException();
        return
            true;
    }

    public function actionDelete()
    {
        $edge_id = Yii::$app->request->post()["edge_id"];

        return $this->findModel($edge_id)->delete();
    }

    protected function findModel($id)
    {
        if (($model = Edge::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
