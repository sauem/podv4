<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\OrdersContact;

/**
 * OrdersContactSearch represents the model behind the search form of `backend\models\OrdersContact`.
 */
class OrdersContactSearch extends OrdersContact
{
    /**
     * {@inheritdoc}
     */
    public $items;
    public function rules()
    {
        return [
            [['id', 'payment_method', 'created_at', 'updated_at'], 'integer'],
            [['name', 'phone', 'code', 'address', 'zipcode', 'email', 'city', 'district', 'order_source', 'note', 'vendor_note', 'status', 'country'], 'safe'],
            [['shipping_cost'], 'number'],
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
        $query = OrdersContact::find();

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
            'shipping_cost' => $this->shipping_cost,
            'payment_method' => $this->payment_method,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'zipcode', $this->zipcode])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'district', $this->district])
            ->andFilterWhere(['like', 'order_source', $this->order_source])
            ->andFilterWhere(['like', 'note', $this->note])
            ->andFilterWhere(['like', 'vendor_note', $this->vendor_note])
            ->andFilterWhere(['IN', 'status', $this->status])
            ->andFilterWhere(['like', 'country', $this->country]);

        return $dataProvider;
    }
}
