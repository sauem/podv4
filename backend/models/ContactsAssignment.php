<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "contacts_assignment".
 *
 * @property int $id
 * @property int|null $user_id
 * @property string|null $phone
 * @property string|null $status
 * @property string|null $country
 * @property int $created_at
 * @property int $updated_at
 */
class ContactsAssignment extends \common\models\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'contacts_assignment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'created_at', 'updated_at'], 'integer'],
            [['created_at', 'updated_at'], 'required'],
            [['phone', 'status', 'country'], 'string', 'max' => 25],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'phone' => 'Phone',
            'status' => 'Status',
            'country' => 'Country',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}