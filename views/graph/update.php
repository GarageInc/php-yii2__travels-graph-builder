<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Graph */

$this->title = 'Update Graph: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Graphs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="graph-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
