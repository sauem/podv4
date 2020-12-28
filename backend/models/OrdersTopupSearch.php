<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\OrdersTopup;

/**
 * OrdersTopupSearch represents the model behind the search form of `backend\models\OrdersTopup`.
 */
class OrdersTopupSearch extends OrdersTopup
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'time', 'partner_id', 'created_at', 'updated_at'], 'integer'],
            [['cash_source'], 'safe'],
            [['total'], 'number'],
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
        $query = OrdersTopup::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'time' => $this->time,
            'partner_id' => $this->partner_id,
            'total' => $this->total,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'cash_source', $this->cash_source])
            ->andFilterWhere(['=', 'partner_id', $this->partner_id]);

        return $dataProvider;
    }
}
