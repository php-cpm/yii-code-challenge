<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\helpers;

use yii\helpers\Html;

/**
 * ZHtml provides a set of static methods for generating commonly used HTML tags.
 *
 * refer to [enum article](https://www.yiiframework.com/wiki/303/drop-down-list-with-enum-values-for-column-of-type-enum-incorporate-into-giix)
 * @author Zou Yi <zoutommy@gmail.com>
 */
class ZHtml extends Html
{
    public static function enumDropDownList($model, $attribute, $htmlOptions=array())
    {
        return Html::activeDropDownList( $model, $attribute, self::enumItem($model,  $attribute), $htmlOptions);
    }

    public static function enumItem($model,$attribute) {
        preg_match('/\((.*)\)/',$model->tableSchema->columns[$attribute]->dbType,$matches);
        foreach(explode("','", $matches[1]) as $value) {
            $value=str_replace("'",null,$value);
            $values[$value]=\Yii::t('enumItem',$value);
        }
        return $values;
    }
}
