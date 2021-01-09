<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "contacts_reason".
 *
 * @property int $id
 * @property string|null $message
 * @property int $created_at
 * @property int $updated_at
 */
class ContactsReason extends \common\models\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'contacts_reason';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['message'], 'string'],
            [['message'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'message' => 'Message',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    static function LISTS()
    {
        $model = ContactsReason::find()->asArray()->all();
        return ArrayHelper::map($model, 'message', 'message');
    }
}
