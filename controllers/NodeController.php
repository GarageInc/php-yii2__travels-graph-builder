<?php

namespace app\controllers;

use app\controllers\base\BaseController;
use app\models\Edge;
use Yii;
use app\models\Node;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * NodeController implements the CRUD actions for Node model.
 */
class NodeController extends BaseController
{


//    /**
//     * Lists all Node models.
//     * @return mixed
//     */
//    public function actionIndex()
//    {
//        $dataProvider = new ActiveDataProvider([
//            'query' => Node::find(),
//        ]);
//
//        return $this->render('index', [
//            'dataProvider' => $dataProvider,
//        ]);
//    }
//
//    /**
//     * Displays a single Node model.
//     * @param integer $id
//     * @return mixed
//     */
//    public function actionView($id)
//    {
//        return $this->render('view', [
//            'model' => $this->findModel($id),
//        ]);
//    }

    /**
     * Creates a new Node model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
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
//
//    /**
//     * Updates an existing Node model.
//     * If update is successful, the browser will be redirected to the 'view' page.
//     * @param integer $id
//     * @return mixed
//     */
//    public function actionUpdate($id)
//    {
//        $model = $this->findModel($id);
//
//        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//            return $this->redirect(['view', 'id' => $model->id]);
//        } else {
//            return $this->render('update', [
//                'model' => $model,
//            ]);
//        }
//    }

    /**
     * Deletes an existing Node model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
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

        return json_encode( $ids);

        Edge::deleteAll('id = :id', [':id' => $ids]);


//        Edge::deleteAll( array('in', 'id', $ids));

//        $node->delete();

    }

    /**
     * Finds the Node model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Node the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Node::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
