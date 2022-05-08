<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use app\components\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SupplierSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Suppliers');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="supplier-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Supplier'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php //echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'id' => 'supplier-table',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'exportOptions' => [],
        'columns' => [
//            [
//                'attribute' => '',
//                'format' => ['raw'],
//                'label' => "全/反选",
//                'header'=>"<input type='checkbox' class='select-on-check-all' name='selection_all' value=1 >",
//                'value' => function ($data) {
//                    return "<input type='checkbox' name='selection[]' value='{$data['id']}' >";
//                },
//            ],
            [
                'class' => \yii\grid\CheckboxColumn::class,
//                'label' => "全/反选",
                'headerOptions' => ['width' => '50','style'=>'cursor:pointer'],
                'contentOptions' => ['align'=>'center'],

            ],
            'id',
            [
                'attribute' => 'name',
                'value' => 'name',
                'filter' => \yii\jui\AutoComplete::widget([
                    'model' => $searchModel,
                    'attribute' => 'name',
                    'clientOptions' => [
                        'minLength' => 2,
                        'autoFill' => true,
                        'source' => new \yii\web\JsExpression('
        function(request, response) {
            jQuery.getJSON("'.Url::to(['supplier/search-name']).'",
            {query: request.term}, function(data) {
                var suggestions = [];
                jQuery.each(data.listdata, function(index, ele) {
                    suggestions.push({
                        label: ele.label,
                        value: ele.value
                    });
                });
                response(suggestions);
            });
        }'
                        ),
                        'select' => new \yii\web\JsExpression('
        function(event, ui) {
            jQuery("#'.Html::getInputId($searchModel, 'name').'")
                .val(ui.item.value);
            jQuery("#supplier-table").yiiGridView("applyFilter");
            }'
                        )
                    ]
                ]),
            ],
            [
                'attribute' => 'code',
                'value' => 'code',
                'filter' => \yii\jui\AutoComplete::widget([
                    'model' => $searchModel,
                    'attribute' => 'code',
                    'clientOptions' => [
                        'minLength' => 1,
                        'autoFill' => true,
                        'source' => new \yii\web\JsExpression('
        function(request, response) {
            jQuery.getJSON("'.Url::to(['supplier/search-code']).'",
            {query: request.term}, function(data) {
                var suggestions = [];
                jQuery.each(data.listdata, function(index, ele) {
                    suggestions.push({
                        label: ele.label,
                        value: ele.value
                    });
                });
                response(suggestions);
            });
        }'
                        ),
                        'select' => new \yii\web\JsExpression('
        function(event, ui) {
            jQuery("#'.Html::getInputId($searchModel, 'code').'")
                .val(ui.item.value);
            jQuery("#supplier-table").yiiGridView("applyFilter");
            }'
                        )
                    ]
                ]),
            ],
//            'code',
//            't_status',
            [
                'attribute' => 't_status',
                'value' => 't_status',
                'filter' => \app\helpers\ZHtml::enumDropDownList($searchModel, 't_status'),
            ],
        ],
    ]);
    ?>

    <?php Pjax::end(); ?>

</div>
