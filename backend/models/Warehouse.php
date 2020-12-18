<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "warehouse".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $country
 * @property string|null $status
 * @property string|null $note
 * @property int $created_at
 * @property int $updated_at
 *
 * @property WarehouseTransaction[] $warehouseTransactions
 */
class Warehouse extends \common\models\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'warehouse';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'country'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['name', 'note'], 'string', 'max' => 255],
            [['country'], 'string', 'max' => 50],
            [['status'], 'string', 'max' => 25],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Tên kho',
            'country' => 'Thị trường',
            'status' => 'Trạng thái',
            'note' => 'Ghi chú',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[WarehouseTransactions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWarehouseTransactions()
    {
        return $this->hasMany(WarehouseTransaction::className(), ['warehouse_id' => 'id']);
    }

    public static function LISTS()
    {
        $all = Warehouse::find()->asArray()->all();
        return ArrayHelper::map($all, 'id', 'name');
    }
}
