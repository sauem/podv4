<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "transporters".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $phone
 * @property string|null $address
 * @property string|null $fax
 * @property string|null $website
 * @property string|null $facebook
 * @property string|null $note
 * @property int $created_at
 * @property int $updated_at
 * @property int|null $transporter_parent
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
            [['website', 'facebook'], 'string'],
            [['name'], 'required'],
            [['created_at', 'updated_at', 'transporter_parent'], 'integer'],
            [['name', 'note'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 25],
            [['address', 'fax'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Tên',
            'phone' => 'Số điện thoại',
            'address' => 'Địa chỉ',
            'fax' => 'Fax',
            'website' => 'Website',
            'facebook' => 'Facebook',
            'note' => 'Ghi chú',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getPartner()
    {
        return $this->hasOne(Transporters::className(), ['transporter_parent' => 'id']);
    }

    public function getChildren()
    {
        return $this->hasMany(Transporters::className(), ['transporter_parent' => 'id']);
    }

    public static function LISTS($all = true)
    {
        $query = Transporters::find();
        if (!$all) {
            $query->filterWhere(['=', 'transporter_parent', 0]);
            $query->orFilterWhere(['IS', 'transporter_parent', new \yii\db\Expression('null')]);
        }
        $result = $query->asArray()->all();
        return ArrayHelper::map($result, 'id', 'name');
    }
}
