<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'My Graphs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="graph-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Graph', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'graphname',
            'user_id',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{build} {update} {delete}',
                'buttons' => [
                    'build' => function ($url,$model) {
                        return Html::a(
                            '<button class="btn btn-primary">Build</button>',
                            $url);
                    },
                    'update' => function ($url,$model) {
                        return Html::a(
                            '<button class="btn btn-primary">Update</button>',
                            $url);
                    },
                    'delete' => function ($url,$model) {
                        return Html::a('Delete', ['delete', 'id' => $model->id], [
                            'class' => 'btn btn-danger',
                            'data' => [
                                'confirm' => 'Are you sure you want to delete this item?',
                                'method' => 'post',
                            ],
                        ]);
                    },
                ]
            ],
        ],
    ]); ?>
</div>
