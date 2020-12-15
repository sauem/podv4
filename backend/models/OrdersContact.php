<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "orders_contact".
 *
 * @property int $id
 * @property string $name
 * @property string $phone
 * @property string|null $code
 * @property string $address
 * @property string $zipcode
 * @property float|null $shipping_cost
 * @property int|null $payment_method
 * @property string|null $email
 * @property string|null $city
 * @property string|null $district
 * @property string|null $order_source
 * @property string|null $note
 * @property string|null $vendor_note
 * @property string|null $status
 * @property int $created_at
 * @property int $updated_at
 *
 * @property OrdersContactSku[] $ordersContactSkus
 */
class OrdersContact extends \common\models\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'orders_contact';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'phone', 'address', 'zipcode', 'created_at', 'updated_at'], 'required'],
            [['shipping_cost'], 'number'],
            [['payment_method', 'created_at', 'updated_at'], 'integer'],
            [['name', 'code', 'address', 'zipcode', 'email', 'city', 'district', 'order_source', 'note', 'vendor_note'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 25],
            [['status'], 'string', 'max' => 100],
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
            'code' => 'Code',
            'address' => 'Address',
            'zipcode' => 'Zipcode',
            'shipping_cost' => 'Shipping Cost',
            'payment_method' => 'Payment Method',
            'email' => 'Email',
            'city' => 'City',
            'district' => 'District',
            'order_source' => 'Order Source',
            'note' => 'Note',
            'vendor_note' => 'Vendor Note',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[OrdersContactSkus]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrdersContactSkus()
    {
        return $this->hasMany(OrdersContactSku::className(), ['order_id' => 'id']);
    }
}
