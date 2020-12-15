<?php

namespace backend\models;

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

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'register_time' => $this->register_time,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'code', $this->name])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'zipcode', $this->zipcode])
            ->andFilterWhere(['like', 'option', $this->option])
            ->andFilterWhere(['like', 'ip', $this->ip])
            ->andFilterWhere(['like', 'note', $this->note])
            ->andFilterWhere(['like', 'partner', $this->partner])
            ->andFilterWhere(['like', 'hash_key', $this->hash_key])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'country', $this->country])
            ->andFilterWhere(['like', 'utm_source', $this->utm_source])
            ->andFilterWhere(['like', 'utm_medium', $this->utm_medium])
            ->andFilterWhere(['like', 'utm_content', $this->utm_content])
            ->andFilterWhere(['like', 'utm_term', $this->utm_term])
            ->andFilterWhere(['like', 'utm_campaign', $this->utm_campaign])
            ->andFilterWhere(['like', 'link', $this->link])
            ->andFilterWhere(['like', 'short_link', $this->short_link]);

        return $dataProvider;
    }
}
