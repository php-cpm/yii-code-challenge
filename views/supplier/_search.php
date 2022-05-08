<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SupplierSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="supplier-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
//        'enableAjaxValidation'=>false,
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-2\">{input}</div>",
//            'labelOptions' => ['class' => 'col-lg-2'],
        ],
        'options' => [
            'class'=>'form-inline',
            'data-pjax' => 1,
        ],
    ]); ?>

    <?= $form->field($model, 'id')->dropdownList([
        '1' => '<10',
        '2' => '>10'
    ],
        ['prompt'=>'Select']) ?>

    <?= $form->field($model, 'name')->textInput([
        'option' => ['maxlength' => true]
    ]) ?>

    <?= $form->field($model, 'code')->textInput([
            'option' => ['maxlength' => true]
    ]) ?>

    <?= $form->field($model, 't_status')->dropDownList([
        'ok' => 'ok',
        'hold' => 'hold'
    ],
        ['prompt'=>'Select']) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
