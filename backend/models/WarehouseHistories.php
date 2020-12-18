<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "warehouse_histories".
 *
 * @property int $id
 * @property int|null $warehouse_id
 * @property string|null $product_sku
 * @property string|null $transaction_type
 * @property int|null $order_code
 * @property string|null $po_code
 * @property int|null $qty
 * @property float|null $total_average
 * @property string|null $note
 * @property int $created_at
 * @property int $updated_at
 */
class WarehouseHistories extends \common\models\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'warehouse_histories';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['warehouse_id', 'order_code', 'qty', 'created_at', 'updated_at'], 'integer'],
            [['total_average'], 'number'],
            [['warehouse_id', 'qty', 'product_sku', 'po_code', 'transaction_type'], 'required'],
            [['product_sku', 'po_code', 'note'], 'string', 'max' => 255],
            [['transaction_type'], 'string', 'max' => 50],
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
            'transaction_type' => 'Transaction Type',
            'order_code' => 'Order Code',
            'po_code' => 'Po Code',
            'qty' => 'Qty',
            'total_average' => 'Total Average',
            'note' => 'Note',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
