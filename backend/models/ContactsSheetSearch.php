<?php

namespace backend\models;

use common\helper\Helper;
use common\models\User;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\ContactsSheet;

/**
 * ContactsSheetSearch represents the model behind the search form of `backend\models\ContactsSheet`.
 */
class ContactsSheetSearch extends ContactsSheet
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'partner_id', 'created_at', 'updated_at'], 'integer'],
            [['category_id', 'contact_source', 'country'], 'safe'],
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
        $query = ContactsSheet::find();
        if(Helper::isRole(UserRole::ROLE_PARTNER)){
            $query->where(['partner_id' => \Yii::$app->user->getId()]);
        }
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
            'partner_id' => $this->partner_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['=', 'category_id', $this->category_id])
            ->andFilterWhere(['like', 'contact_source', $this->contact_source])
            ->andFilterWhere(['like', 'country', $this->country]);

        return $dataProvider;
    }
}
