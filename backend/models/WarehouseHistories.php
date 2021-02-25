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
    const TYPE_INPUT = 'input';

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
            [['warehouse_id', 'qty', 'created_at', 'order_code', 'updated_at'], 'integer'],
            [['total_average'], 'number'],
            [['product_sku', 'qty'], 'required'],
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
     * @param $warehouse_id
     * @param $sku
     * @param $qty
     * @param $po_code
     * @param $type
     * @throws BadRequestHttpException
     */
    public static function createRow($warehouse_id, $sku, $qty, $po_code, $type)
    {
        try {
            $transaction = WarehouseTransaction::findOne(['warehouse_id' => $warehouse_id, 'product_sku' => $sku, 'po_code' => $po_code]);
            if (!$transaction) {
                throw new BadRequestHttpException("Không tồn tại mã nhập hàng này!");
            }
            $model = new WarehouseHistories();
            $model->warehouse_id = $warehouse_id;
            $model->product_sku = $sku;
            $model->qty = $qty;
            $model->transaction_type = $type;
            $model->total_average = $transaction->total_average / $transaction->qty * $qty;
            if (!$model->save()) {
                throw new BadRequestHttpException(Helper::firstError($model));
            }

        } catch (\Exception $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }
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
                $model = WarehouseHistories::findOne(['code' => $orderCode, 'product_sku' => $sku]);
                if (!$model) {
                    $model = new WarehouseHistories();
                }
                $model->code = $orderCode;
                $model->transaction_type = $type;
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
