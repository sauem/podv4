<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "transporters".
 *
 * @property int $id
 * @property int|null $name
 * @property string|null $phone
 * @property string|null $address
 * @property string|null $fax
 * @property string|null $website
 * @property string|null $facebook
 * @property string|null $note
 * @property int $created_at
 * @property int $updated_at
 */
class Transporters extends \common\models\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'transporters';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'created_at', 'updated_at'], 'integer'],
            [['website', 'facebook'], 'string'],
            [['created_at', 'updated_at'], 'required'],
            [['phone'], 'string', 'max' => 25],
            [['address', 'fax'], 'string', 'max' => 50],
            [['note'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'phone' => 'Phone',
            'address' => 'Address',
            'fax' => 'Fax',
            'website' => 'Website',
            'facebook' => 'Facebook',
            'note' => 'Note',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
