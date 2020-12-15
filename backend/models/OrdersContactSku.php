<?php

namespace backend\models;

use common\helper\Helper;
use Yii;
use yii\web\BadRequestHttpException;

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
            [['sku', 'order_id', 'qty', 'price'], 'required'],
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

    /**
     * @param $orderId
     * @param array $items
     * @throws BadRequestHttpException
     */
    static function saveItems($orderId, $items = [])
    {
        try {
            if (empty($items)) {
                throw new BadRequestHttpException('Không có sản phẩm nào được chọn!');
            }
            foreach ($items as $item) {
                $model = new OrdersContactSku();
                $model->order_id = $orderId;
                $model->sku = $item['sku'];
                $model->price = Helper::toFloat($item['price']);
                $model->qty = $item['qty'];
                if (!$model->save()) {
                    throw new BadRequestHttpException(Helper::firstError($model));
                }
            }
        } catch (\Exception $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }
    }
}
