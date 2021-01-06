<?php

namespace backend\models;

use common\helper\Helper;
use Yii;
use yii\web\BadRequestHttpException;

/**
 * This is the model class for table "warehouse_storage".
 *
 * @property int $id
 * @property string|null $po_code
 * @property int|null $warehouse_id
 * @property int|null $qty
 * @property string|null $sku
 * @property int $created_at
 * @property int $updated_at
 */
class WarehouseStorage extends \common\models\BaseModel
{
    /**
     * {@inheritdoc}
     */
    const TYPE_PLUS = 'plus';
    const TYPE_MINUS = 'minus';

    public static function tableName()
    {
        return 'warehouse_storage';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['warehouse_id', 'qty', 'created_at', 'updated_at'], 'integer'],
            [['warehouse_id', 'qty', 'sku'], 'required'],
            [['po_code', 'sku'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'po_code' => 'Po Code',
            'warehouse_id' => 'Warehouse ID',
            'qty' => 'Qty',
            'sku' => 'Sku',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }


    // #CC001
    // SKU :  SKU1x 5, SKU2 x 4
    // transaction 1: SKU1 x 4 | warehouse 1 | PO-001
    // transaction 2: SKU1 x 5 | warehouse 2 | PO-002
    // transaction 3:  SKU2 x 10 | PO-003
    // Storage :  SKU1 x 4 | warehouse 1
    // Storage :  SKU1 x 5 | warehouse 2
    // Storage :  SKU10 x 10 | warehouse 2
    //
    /**
     * @param $warehouse_id
     * @param $sku
     * @param $qty
     * @param $type
     * @throws BadRequestHttpException
     */
    public static function updateStorage($warehouse_id, $sku, $qty, $po_code, $type, $history = false)
    {
        $storage = WarehouseStorage::findOne(['warehouse_id' => $warehouse_id, 'sku' => $sku]);
        if (!$storage) {
            $storage = new WarehouseStorage();
        }
        $transactionType = null;
        try {
            switch ($type) {
                //user for create order
                case static::TYPE_MINUS:
                    if ($storage->qty < $qty) {
                        $qty_amount = $qty - $storage->qty;
                        $nextQty = static::nextStorage($warehouse_id, $sku, $qty_amount);
                        if (!$nextQty) {
                            throw new BadRequestHttpException("Số lượng sản phẩm không khả dụng!");
                        }
                        //update store 1
                        $storage->qty = 0;
                        //update storage 2
                        $nextQty->qty = $nextQty->qty - $qty_amount;
                        if (!$nextQty->save()) {
                            throw new BadRequestHttpException(Helper::firstError($nextQty));
                        }
                    } else {
                        $storage->qty = $storage->qty - $qty;
                    }
                    $transactionType = WarehouseHistories::TYPE_OUTPUT;
                    break;
                case static::TYPE_PLUS:
                    $storage->qty = $storage->qty + $qty;
                    $storage->warehouse_id = $warehouse_id;
                    $storage->po_code = $po_code;
                    $storage->sku = $sku;
                    $transactionType = WarehouseHistories::TYPE_INPUT;
                    break;
            }
            if (!$storage->save()) {
                throw new BadRequestHttpException(Helper::firstError($storage));
            }
            if ($history) {
                WarehouseHistories::createRow(
                    $warehouse_id,
                    $sku,
                    $po_code,
                    $qty,
                    $transactionType);
            }

        } catch (\Exception $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }
    }

    /**
     * @param $prev_warehouse
     * @param $sku
     * @param $amount_qty
     * @return bool|mixed|null
     */
    static function nextStorage($prev_warehouse, $sku, $amount_qty)
    {
        $model = WarehouseStorage::find()
            ->where(['sku' => $sku])
            ->andWhere(['<>', 'warehouse_id', $prev_warehouse])
            ->andWhere(['>=', 'qty', $amount_qty])
            ->orderBy('created_at')
            ->one();
        if (!$model) {
            return false;
        }
        return $model;
    }
}
