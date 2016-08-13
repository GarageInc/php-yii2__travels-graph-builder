<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Edge */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="edge-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'node_first_id')->textInput() ?>

    <?= $form->field($model, 'node_second_id')->textInput() ?>

    <?= $form->field($model, 'graph_id')->textInput() ?>

    <?= $form->field($model, 'weight')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
