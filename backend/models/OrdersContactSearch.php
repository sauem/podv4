<?php

namespace backend\models;

use common\helper\Helper;
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
        if(Helper::isRole(UserRole::ROLE_PARTNER)){
            $query->innerJoin('contacts','contacts.code=orders_contact.code')
                ->andFilterWhere([
                    'contacts.partner' => \Yii::$app->user->identity->username
                ]);
        }
        $query->andFilterWhere(['like', 'orders_contact.name', $this->name])
            ->andFilterWhere(['like', 'orders_contact.phone', $this->phone])
            ->andFilterWhere(['IN', 'orders_contact.id', $this->id])
            ->andFilterWhere(['like', 'orders_contact.code', $this->code])
            ->andFilterWhere(['like', 'orders_contact.address', $this->address])
            ->andFilterWhere(['like', 'orders_contact.zipcode', $this->zipcode])
            ->andFilterWhere(['like', 'orders_contact.email', $this->email])
            ->andFilterWhere(['like', 'orders_contact.city', $this->city])
            ->andFilterWhere(['like', 'orders_contact.district', $this->district])
            ->andFilterWhere(['like', 'orders_contact.order_source', $this->order_source])
            ->andFilterWhere(['like', 'orders_contact.note', $this->note])
            ->andFilterWhere(['like', 'orders_contact.vendor_note', $this->vendor_note])
            ->andFilterWhere(['IN', 'orders_contact.status', $this->status])
            ->andFilterWhere(['IN', 'orders_contact.payment_status', $this->payment_status])
            ->andFilterWhere(['like', 'orders_contact.country', $this->country]);

        return $dataProvider;
    }
}
