<?php

namespace app\controllers;

use Yii;
use app\models\Edge;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * EdgeController implements the CRUD actions for Edge model.
 */
class EdgeController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),

            ],

            'acess' => [
                'class' => AccessControl::className(),
                'only' => ['delete', 'create'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => false,
                        'actions' => ['delete', 'create'],
                        'roles' => ['?'],
                    ],
                ],
            ],
        ];
    }
//
//    /**
//     * Lists all Edge models.
//     * @return mixed
//     */
//    public function actionIndex()
//    {
//        $dataProvider = new ActiveDataProvider([
//            'query' => Edge::find(),
//        ]);
//
//        return $this->render('index', [
//            'dataProvider' => $dataProvider,
//        ]);
//    }
//
//    /**
//     * Displays a single Edge model.
//     * @param integer $id
//     * @return mixed
//     */
//    public function actionView($id)
//    {
//        return $this->render('view', [
//            'model' => $this->findModel($id),
//        ]);
//    }


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

        $model->save();

        return $model->getPrimaryKey();
    }
//
//    /**
//     * Updates an existing Edge model.
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
//
    /**
     * Deletes an existing Edge model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return "blablabla";
    }

    /**
     * Finds the Edge model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Edge the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Edge::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
