<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "contacts_log_status".
 *
 * @property int $id
 * @property string|null $code
 * @property int|null $user_id
 * @property string|null $phone
 * @property string|null $status
 * @property string|null $sale_note
 * @property string|null $customer_note
 * @property int $created_at
 * @property int $updated_at
 */
class ContactsLogStatus extends \common\models\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'contacts_log_status';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'created_at', 'updated_at'], 'integer'],
            [['created_at', 'updated_at'], 'required'],
            [['code', 'sale_note', 'customer_note'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 25],
            [['status'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'Code',
            'user_id' => 'User ID',
            'phone' => 'Phone',
            'status' => 'Status',
            'sale_note' => 'Sale Note',
            'customer_note' => 'Customer Note',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
