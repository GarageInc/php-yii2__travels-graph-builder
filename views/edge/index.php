<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Edges';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="edge-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Edge', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'node_first_id',
            'node_second_id',
            'graph_id',
            'weight',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
