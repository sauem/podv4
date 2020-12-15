<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "orders_contact_sku".
 *
 * @property int $id
 * @property int|null $order_id
 * @property string $sku
 * @property int|null $qty
 * @property float|null $price
 * @property int $created_at
 * @property int $updated_at
 *
 * @property OrdersContact $order
 */
class OrdersContactSku extends \common\models\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'orders_contact_sku';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'qty', 'created_at', 'updated_at'], 'integer'],
            [['sku', 'created_at', 'updated_at'], 'required'],
            [['price'], 'number'],
            [['sku'], 'string', 'max' => 255],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => OrdersContact::className(), 'targetAttribute' => ['order_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'sku' => 'Sku',
            'qty' => 'Qty',
            'price' => 'Price',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Order]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(OrdersContact::className(), ['id' => 'order_id']);
    }
}
