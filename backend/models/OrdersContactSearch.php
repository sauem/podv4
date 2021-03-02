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
    public $register_time;
    public $filter;
    public $name;

    public function rules()
    {
        return [
            [['status', 'filter'], 'string'],
            [['items', 'payment_method', 'register_time', 'warehouse_id', 'name'], 'safe'],
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
        $query = OrdersContact::find()->with(['skuItems', 'warehouse', 'transporter', 'payment']);
        // add conditions that should always apply here
        if (Helper::isRole(UserRole::ROLE_PARTNER)) {
            $query->innerJoin('contacts', 'contacts.code=orders_contact.code')
                ->where([
                    'contacts.partner' => \Yii::$app->user->identity->username
                ]);
        }


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if ($this->items) {
            $query->innerJoin('orders_contact_sku', 'orders_contact_sku.order_id = orders_contact.id')
                ->andFilterWhere(['IN', 'orders_contact_sku.sku', $this->items]);
        }
        if ($this->register_time) {
            $time_register = explode(' - ', $this->register_time);
            $startTime = Helper::timer(str_replace('/', '-', $time_register[0]));
            $endTime = Helper::timer(str_replace('/', '-', $time_register[1]), 1);
            $query->innerJoin('contacts', 'contacts.code = orders_contact.code');
            $query->andFilterWhere(['between', 'contacts.register_time', $startTime, $endTime]);
        }

//
//        if (!$this->validate()) {
//            // uncomment the following line if you do not want to return any records when validation fails
//            // $query->where('0=1');
//            return $dataProvider;
//        }


        $query->andFilterWhere(['IN', '{{orders_contact}}.payment_method', $this->payment_method])
            ->andFilterWhere(['IN', '{{orders_contact}}.status', $this->status])
            ->andFilterWhere(['IN', '{{orders_contact}}.warehouse_id', $this->warehouse_id]);
        if (!empty($this->name)) {
            $query->andFilterWhere(['LIKE', '{{orders_contact}}.name', $this->name]);
            $query->orFilterWhere(['LIKE', '{{orders_contact}}.phone', $this->name]);
            $query->orFilterWhere(['LIKE', '{{orders_contact}}.code', $this->name]);
        }
        return $dataProvider;
    }
}
