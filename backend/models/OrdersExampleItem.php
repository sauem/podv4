<?php

namespace backend\models;

use common\helper\Helper;
use Yii;
use yii\web\BadRequestHttpException;

/**
 * This is the model class for table "orders_example_item".
 *
 * @property int $id
 * @property int|null $order_example_id
 * @property string|null $sku
 * @property int|null $qty
 * @property float|null $price
 * @property int $created_at
 * @property int $updated_at
 *
 * @property OrdersExample $orderExample
 */
class OrdersExampleItem extends \common\models\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'orders_example_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_example_id', 'qty', 'created_at', 'updated_at'], 'integer'],
            [['price'], 'number'],
            [['order_example_id', 'qty', 'price', 'sku'], 'required'],
            [['sku'], 'string', 'max' => 255],
            [['order_example_id'], 'exist', 'skipOnError' => true, 'targetClass' => OrdersExample::className(), 'targetAttribute' => ['order_example_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_example_id' => 'Order Example ID',
            'sku' => 'Sku',
            'qty' => 'Qty',
            'price' => 'Price',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[OrderExample]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrderExample()
    {
        return $this->hasOne(OrdersExample::className(), ['id' => 'order_example_id']);
    }

    /**
     * @param $exampleId
     * @param array $items
     * @throws BadRequestHttpException
     */
    public static function saveItems($exampleId, $items = [])
    {
        try {
            if (empty($items)) {
                throw new BadRequestHttpException('Không có sản phẩm nào được chọn!');
            }
            foreach ($items as $item) {
                $product = Products::findOne(['sku' => $item['sku']]);
                if (!$product) {
                    throw new BadRequestHttpException("không tìm thấy sản phẩm có sẵn!");
                }
                $model = new OrdersExampleItem();
                $model->order_example_id = $exampleId;
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
