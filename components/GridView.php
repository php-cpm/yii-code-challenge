<?php


namespace app\components;


use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use \Yii;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\View;

class GridView extends \yii\grid\GridView
{
    /**
     * @var string the layout that determines how different sections of the grid view should be organized.
     * The following tokens will be replaced with the corresponding section contents:
     *
     * - `{summary}`: the summary section. See [[renderSummary()]].
     * - `{errors}`: the filter model error summary. See [[renderErrors()]].
     * - `{items}`: the list items. See [[renderItems()]].
     * - `{sorter}`: the sorter. See [[renderSorter()]].
     * - `{pager}`: the pager. See [[renderPager()]].
     */
    public $layout = '{summary} <div class="float-right">{export}</div> <div>{items}</div>{pager}';


    /**
     * @var string the HTML content to be displayed as the export of the list view.
     * If you do not want to show the export, you may set it with an empty string.
     *
     * The following tokens will be replaced with the corresponding values:
     *
     * - `{begin}`: the starting row number (1-based) currently being displayed
     * - `{end}`: the ending row number (1-based) currently being displayed
     * - `{count}`: the number of rows currently being displayed
     * - `{totalCount}`: the total number of rows available
     * - `{page}`: the page number (1-based) current being displayed
     * - `{pageCount}`: the number of pages available
     */
    public $export = true;
    /**
     * @var array the HTML attributes for the export of the list view.
     * The "tag" element specifies the tag name of the export element and defaults to "div".
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $exportOptions = ['class' => 'export'];
    public $summaryOptions = ['class' => 'summary float-left'];

    /**
     * {@inheritdoc}
     */
    public function renderSection($name)
    {
        switch ($name) {
            case '{errors}':
                return $this->renderErrors();
            case '{export}':
                return $this->renderExport();
            default:
                return parent::renderSection($name);
        }
    }

    public function renderExport()
    {
        $exportContent = $this->export;
        if ($exportContent === false) {
            return '';
        }
        $exportOptions = $this->exportOptions;
        $count = $this->dataProvider->getCount();
        $pagination = $this->dataProvider->getPagination();
        $tag = ArrayHelper::remove($exportOptions, 'tag', 'div');

        if($pagination === false) {
            $begin = $page = $pageCount = 1;
            $end = $totalCount = $count;
        } else {
            $totalCount = $this->dataProvider->getTotalCount();
            $begin = $pagination->getPage() * $pagination->pageSize + 1;
            $end = $begin + $count - 1;
            if ($begin > $end) {
                $begin = $end;
            }
            $page = $pagination->getPage() + 1;
            $pageCount = $pagination->pageCount;
        }

        $id = $this->options['id'] ?? 'grid';
        $exportAction = $this->options['exportUrl'] ?? Url::toRoute([Yii::$app->controller->id . '/export']);
        $this->getView()->registerJs("
$('[name=\'selection[]\']').on('change', function(){
    var CheckedLabel = jQuery('.checked-label');
    if(CheckedLabel.html() == null){
        $('<div class=\'checked-label\'></div><input type=\'hidden\' id=\'ExportAllIsChecked\' />').prependTo($('#$id'));
    }
    jQuery('#ExportAllIsChecked').val(0);
    var rows = jQuery('#$id').yiiGridView('getSelectedRows');
    $('.checked-label').html(rows.length + ' items selected');
});
$('.select-on-check-all').on('change', function(){
    var CheckedLabel = jQuery('.checked-label');
    if(CheckedLabel.html() == null){
        $('<div class=\'checked-label\'></div><input type=\'hidden\' id=\'ExportAllIsChecked\' />').prependTo($('#$id'));
    }
    var rows = jQuery('#$id').yiiGridView('getSelectedRows');
    jQuery('#ExportAllIsChecked').val(0);
    if($(this).prop('checked') == true) {
        $('.checked-label').html(rows.length + ' items in this page selected.' + ' <span class=\'btn-link select-all-data\' >select all conversations that match this search</span>');
    }else {
        $('.checked-label').html('0 items selected');
    }
});

$( document ).on('click', '.select-all-data', function(){
    jQuery('#ExportAllIsChecked').val(1);
    $('.checked-label').html('All conversations in this search have been selected.' + ' <span class=\'btn-link unselect-all-data\' > clear selection</span>');
});
$( document ).on('click', '.unselect-all-data', function(){
    var rows = jQuery('#$id').yiiGridView('getSelectedRows');
    jQuery('#ExportAllIsChecked').val(0);
    $('.checked-label').html(rows.length + ' items in this page selected.' + ' <span class=\'btn-link select-all-data\' >select all conversations that match this search</span>');
});
$('#export-btn').on('click', function(){
    var rows = jQuery('#$id').yiiGridView('getSelectedRows');
    if(rows.length == 0) {
        alert('no checked rows.')
        return 
    }
    var isChecked = jQuery('#ExportAllIsChecked').val();
    var url = '$exportAction';
    var jQform = $(\"<form id='csv-download' style='display: none;' method='post'></form>\");
    jQform.attr(\"action\",url);
    $('body').append(jQform);
    var settings = jQuery('#$id').yiiGridView('data').settings
    var data = {};
    $.each($(settings.filterSelector).serializeArray(), function () {
        if (!(this.name in data)) {
            data[this.name] = [];
        }
        data[this.name].push(this.value);
    });
    var jQinput1 = $(\"<input name='data' type='text' />\");
    jQinput1.attr(\"value\",JSON.stringify(data));
    $(\"#csv-download\").append(jQinput1);
    var jQinput1 = $(\"<input name='rows' type='text' />\");
    jQinput1.attr(\"value\",JSON.stringify(rows));
    $(\"#csv-download\").append(jQinput1);
    var jQinput2 = $(\"<input name='checkall' type='text' />\");
    jQinput2.attr(\"value\",isChecked);
    $('#csv-download').append(jQinput2);
    jQform.submit();
});",View::POS_READY);


        $options = Json::encode([
        ]);
        if ($exportContent == true) {
            $exportContent = Html::button('Export', [
                'class' => 'btn btn-primary export-btn',
                'id' => 'export-btn',
            ]);
        }
        return Html::tag($tag, Yii::$app->getI18n()->format($exportContent, [
            'begin' => $begin,
            'end' => $end,
            'count' => $count,
            'totalCount' => $totalCount,
            'page' => $page,
            'pageCount' => $pageCount,
        ], Yii::$app->language), $exportOptions);
    }

}
