<?php

namespace app\controllers;

use app\models\Node;
use app\pathfinders\DijkstraAlgorithm;
use Yii;
use app\models\Graph;
use yii\data\ActiveDataProvider;
use yii\rest\ActiveController;
use yii\web\Controller;
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
    // указываем класс модели, который будет использоваться
    public $modelClass = 'app\models\Graph';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {

        return
            \yii\helpers\ArrayHelper::merge(parent::behaviors(),  [
//            'verbs' => [
//                'class' => VerbFilter::className(),
//                'actions' => [
//                    'delete' => ['POST'],
//                ],
//            ],
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
//
//    public static function isValidGraph($graph){
//
//        if($graph->user_id && $graph->graphname){
//            return true;
//        }
//
//        return false;
//    }
//
//    /**
//     * Lists all Graph models.
//     * @return mixed
//     */
//    public function actionIndex()
//    {
//        if ( Yii::$app->user->isGuest) {
//            return $this->goBack();
//        }
//
//        $userId = Yii::$app->user->identity->id;
//
//        $dataProvider = new ActiveDataProvider([
//            'query' => Graph::find()->where(['user_id' => $userId])
//        ]);
//
//        return $this->render('index', [
//            'dataProvider' => $dataProvider,
//        ]);
//    }
//
//    /**
//     * Displays a single Graph model.
//     * @param integer $id
//     * @return mixed
//     */
////    public function actionView($id)
////    {
////        return $this->render('view', [
////            'model' => $this->findModel($id),
////        ]);
////    }
//
//    /**
//     * Creates a new Graph model.
//     * If creation is successful, the browser will be redirected to the 'view' page.
//     * @return mixed
//     */
//    public function actionCreate()
//    {
//        $model = new Graph();
//
//        $userId = Yii::$app->user->identity->id;
//
//        $model->load(Yii::$app->request->post());
//
//        $model->user_id = $userId;
//
//        if (self::isValidGraph($model) && $model->save()) {
//            return $this->redirect(['index', 'id' => $model->id]);
//        } else {
//            return $this->render('create', [
//                'model' => $model,
//            ]);
//        }
//    }
//
//
//    /**
//     * Updates an existing Graph model.
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
//
//    public function actionBuild( $id)
//    {
//        $model = $this->findModel( $id);
//
//        return $this->render( 'build', [
//            'graph' => $model,
//            'nodes' => $model->getNodes()->all()
//        ]);
//    }
//
//    /**
//     * Deletes an existing Graph model.
//     * If deletion is successful, the browser will be redirected to the 'index' page.
//     * @param integer $id
//     * @return mixed
//     */
//    public function actionDelete($id)
//    {
//        $this->findModel($id)->delete();
//
//        return $this->redirect(['index']);
//    }
//
//    /**
//     * Finds the Graph model based on its primary key value.
//     * If the model is not found, a 404 HTTP exception will be thrown.
//     * @param integer $id
//     * @return Graph the loaded model
//     * @throws NotFoundHttpException if the model cannot be found
//     */
//    protected function findModel($id)
//    {
//        if (($model = Graph::findOne($id)) !== null) {
//            return $model;
//        } else {
//            throw new NotFoundHttpException('The requested page does not exist.');
//        }
//    }
//
//    public function actionItem( $graph_id, $node_first_id, $node_second_id)
//    {
//        $result = array(
//            '$graph_id'  => $graph_id,
//            '$node_first_id' => $node_first_id,
//            '$node_second_id' => $node_second_id
//        );
//
//        return json_encode($result, JSON_UNESCAPED_UNICODE);
//    }
//
//    public function convertModelToArray($models, $fields) {
//
//        $result = array();
//
//        foreach ($models as $model) {
//            $row = array();
//
//            foreach ($fields as $field){
//                $row[$field] = $model->getAttribute( $field);
//            }
//
//            array_push($result, $row);
//        }
//
//        return $result;
//    }
//
//    public function actionStructure( $graph_id )
//    {
//
//        $graph = $this::findModel($graph_id);
//
//        $nodes = $graph->getNodes()->all();
//        $edges = $graph->getEdges()->all();
//
//
//        $result = [
//            "nodes" => $this::convertModelToArray($nodes, ["id", "nodename"]),
//            "edges" => $this::convertModelToArray($edges, ["id", 'node_first_id', 'node_second_id', 'weight'])
//        ];
//
//        return json_encode($result);
//    }
//
//
//    public function actionFindpath( $graph_id, $node_first_id, $node_second_id)
//    {
//        $graph = $this::findModel( $graph_id);
//
//        // can use id as alias, because graph is small, BUT IT BAD PRACTICE. TODO BLEAT.
//        $edges = $this::convertModelToArray( $graph->getEdges()->all(), ["node_first_id", "node_second_id", "weight" ]);
//
//        $dist_array = array();
//
//        foreach ($edges as $edge){
//            $dist_array[$edge["node_first_id"]][$edge["node_second_id"]] = $edge["weight"];
//            $dist_array[$edge["node_second_id"]][$edge["node_first_id"]] = $edge["weight"];
//        }
//
//        $result = DijkstraAlgorithm::findPath($dist_array,$node_first_id,$node_second_id);
//
//        return json_encode($result);
//    }
}
