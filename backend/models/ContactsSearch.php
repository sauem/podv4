<?php

namespace backend\models;

use common\helper\Helper;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Contacts;

/**
 * ContactsSearch represents the model behind the search form of `backend\models\Contacts`.
 */
class ContactsSearch extends Contacts
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'register_time', 'created_at', 'updated_at'], 'integer'],
            [['code', 'phone', 'name', 'email', 'address', 'zipcode', 'option', 'ip', 'note', 'partner', 'hash_key', 'status', 'country', 'utm_source', 'utm_medium', 'utm_content', 'utm_term', 'utm_campaign', 'link', 'short_link'], 'safe'],
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
        $query = Contacts::find();
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
        if (Helper::isRole(UserRole::ROLE_PARTNER)) {
            $query->andFilterWhere(['partner' => \Yii::$app->user->identity->username]);
        }

        $query->andFilterWhere(['like', 'contacts.code', $this->name])
            ->orFilterWhere(['like', 'contacts.name', $this->name])
            ->orFilterWhere(['like', 'contacts.phone', $this->name])
            ->orFilterWhere(['like', 'contacts.email', $this->name])
            ->orFilterWhere(['like', 'contacts.partner', $this->name])
            ->andFilterWhere(['contacts.status' => $this->status])
            ->andFilterWhere(['contacts.phone' => $this->phone]);
        return $dataProvider;
    }
}
