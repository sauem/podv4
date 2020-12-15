<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "warehouse_transaction".
 *
 * @property int $id
 * @property int|null $warehouse_id
 * @property string|null $product_sku
 * @property int|null $qty
 * @property string|null $transaction_type
 * @property float|null $total_average
 * @property string|null $po_code
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Warehouse $warehouse
 */
class WarehouseTransaction extends \common\models\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'warehouse_transaction';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['warehouse_id', 'qty', 'created_at', 'updated_at'], 'integer'],
            [['total_average'], 'number'],
            [['created_at', 'updated_at'], 'required'],
            [['product_sku', 'po_code'], 'string', 'max' => 255],
            [['transaction_type'], 'string', 'max' => 50],
            [['warehouse_id'], 'exist', 'skipOnError' => true, 'targetClass' => Warehouse::className(), 'targetAttribute' => ['warehouse_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'warehouse_id' => 'Warehouse ID',
            'product_sku' => 'Product Sku',
            'qty' => 'Qty',
            'transaction_type' => 'Transaction Type',
            'total_average' => 'Total Average',
            'po_code' => 'Po Code',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Warehouse]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWarehouse()
    {
        return $this->hasOne(Warehouse::className(), ['id' => 'warehouse_id']);
    }
}
