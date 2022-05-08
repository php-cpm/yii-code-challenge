<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Supplier;

/**
 * SupplierSearch represents the model behind the search form of `app\models\Supplier`.
 */
class SupplierSearch extends Supplier
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['name', 'code', 't_status'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Supplier::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);


        // grid filtering conditions
        $operator = self::getOperator($this->id);
        $query->andFilterWhere([$operator, 'id', str_replace($operator,'',$this->id)]);

//        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
//            return $dataProvider;
//        }
        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 't_status', $this->t_status]);

        return $dataProvider;
    }
    public static function getOperator($qryString){
        switch ($qryString){
            case strpos($qryString,'<>') === 0:
                $operator = '<>';
                break;
            case strpos($qryString,'!=') === 0:
                $operator = '!=';
                break;
            case strpos($qryString,'>=') === 0:
                $operator = '>=';
                break;
            case strpos($qryString,'<=') === 0:
                $operator = '<=';
                break;
            case strpos($qryString,'=') === 0:
                $operator = '=';
                break;
            case strpos($qryString,'>') === 0:
                $operator = '>';
                break;
            case strpos($qryString,'<') === 0:
                $operator = '<';
                break;
            default:
                $operator =  'like';
                break;
        }
        return $operator;
    }
}
