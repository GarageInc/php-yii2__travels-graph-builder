<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Graph */

$this->title = 'Create Graph';

$this->params['breadcrumbs'][] = ['label' => 'Graphs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="graph-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
