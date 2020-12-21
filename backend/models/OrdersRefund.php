<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "orders_refund".
 *
 * @property int $id
 * @property string $code
 * @property string|null $time_refund_success
 * @property string|null $checking_number
 * @property string|null $sku
 * @property int|null $qty
 * @property string|null $po_code
 * @property float|null $total
 * @property int $created_at
 * @property int $updated_at
 */
class OrdersRefund extends \common\models\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'orders_refund';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['qty', 'created_at', 'updated_at'], 'integer'],
            [['total'], 'number'],
            [['code', 'sku'], 'required'],
            [['time_refund_success', 'sku', 'code'], 'string', 'max' => 255],
            [['checking_number', 'po_code'], 'string', 'max' => 50],
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
            'time_refund_success' => 'Time Refund Success',
            'checking_number' => 'Checking Number',
            'sku' => 'Sku',
            'qty' => 'Qty',
            'po_code' => 'Po Code',
            'total' => 'Total',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
