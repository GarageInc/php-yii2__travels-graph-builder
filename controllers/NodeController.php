<?php

namespace app\controllers;

use app\controllers\base\BaseController;
use app\models\Edge;
use app\models\Graph;
use Yii;
use app\models\Node;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class NodeController extends BaseController
{

    public function actionCreate()
    {
        $model = new Node();

        $model->load(Yii::$app->request->post());

        $model->nodename = Yii::$app->request->post("nodename");
        $model->graph_id = Yii::$app->request->post("graph_id");

        if( $model->save())
            return json_encode([
                'id' => $model->id,
                'nodename' => $model->nodename
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
        $id = Yii::$app->request->post()["node_id"];

        $node = $this->findModel($id);

        $firstEdges = $node->getEdgesAsFirst()->all();

        $ids = array();

        foreach ($firstEdges as $edge){
            $ids[] = $edge->getPrimaryKey();
        }

        $secondEdges = $node->getEdgesAsSecond()->all();

        foreach ($secondEdges as $edge){
            $ids[] = $edge->getPrimaryKey();
        }

//        return json_encode( $ids);

//        Edge::deleteAll('id = :id', [':id' => $ids]);


        Edge::deleteAll( array('in', 'id', $ids));

        return $node->delete();
    }

    protected function findModel($id)
    {
        if (($model = Node::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
