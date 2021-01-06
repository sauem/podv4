<?php

namespace backend\models;

use common\helper\Helper;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;

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
 * @property string|null $code
 */
class WarehouseHistories extends \common\models\BaseModel
{
    const TYPE_OUTPUT = 'output';
    const TYPE_REFUND = 'refund';

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
            [['created_at', 'updated_at'], 'required'],
            [['product_sku', 'po_code', 'note', 'code'], 'string', 'max' => 255],
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
            'code' => 'Code',
        ];
    }

    /**
     * @param $orderCode
     * @param $items
     * @param string $type
     * @throws BadRequestHttpException
     */
    public static function saveHistories($orderCode, $items, $type = WarehouseHistories::TYPE_OUTPUT)
    {
        try {
            if (Helper::isEmpty($items)) {
                throw new BadRequestHttpException("Không có sản phẩm được chọn!");
            }

            foreach ($items as $item) {
                $sku = ArrayHelper::getValue($item, 'sku', null);
                $qty = ArrayHelper::getValue($item, 'qty', null);
                $price = ArrayHelper::getValue($item, 'price', null);
                $model = new WarehouseHistories();
                $model->code = $orderCode;
                $model->transaction_type = $type;
                $model->order_code = $orderCode;
                $model->product_sku = $sku;
                $model->qty = $qty;
                $model->total_average = Helper::toFloat($price);
                $model->warehouse_id = null;
                if (!$model->save()) {
                    throw new BadRequestHttpException(Helper::firstError($model));
                }
            }
        } catch
        (\Exception $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }
    }
}
