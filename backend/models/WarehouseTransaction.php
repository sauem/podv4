<?php

namespace backend\models;

use common\helper\Helper;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\BadRequestHttpException;

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
    const TRANSACTION_IMPORT = 'import';
    const TRANSACTION_EXPORT = 'export';

    const TRANSACTION_TYPE = [
        self::TRANSACTION_IMPORT => 'Nhập kho',
        self::TRANSACTION_EXPORT => 'Xuất kho'
    ];

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
            [['warehouse_id', 'product_sku', 'qty', 'total_average'], 'required'],
            [['product_sku', 'po_code'], 'string', 'max' => 255],
            [['transaction_type'], 'string', 'max' => 50],
            [['warehouse_id'], 'exist', 'skipOnError' => true, 'targetClass' => Warehouse::className(), 'targetAttribute' => ['warehouse_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getProduct()
    {
        return $this->hasOne(Products::className(), ['sku' => 'product_sku'])->with('category');
    }


    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'warehouse_id' => 'Kho',
            'product_sku' => 'Sản phẩm',
            'qty' => 'Số lượng',
            'transaction_type' => 'Loại giao dịch',
            'total_average' => 'Tổng vốn',
            'po_code' => 'Mã giao dịch',
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

    public static function TransactionLabel($type)
    {
        switch ($type) {
            case self::TRANSACTION_EXPORT:
                $color = 'danger';
                break;
            default:
                $color = 'success';
                break;
        }
        return Html::tag('span', ArrayHelper::getValue(self::TRANSACTION_TYPE, $type, '---'), [
            "class" => "badge badge-$color badge-pill m-auto"
        ]);
    }

    public function afterDelete()
    {
        parent::afterDelete(); // TODO: Change the autogenerated stub
        WarehouseStorage::deleteAll(['warehouse_id' => $this->warehouse_id, 'sku' => $this->product_sku, 'po_code' => $this->po_code]);
    }

    public function beforeSave($insert)
    {

        $lastID = WarehouseTransaction::find()->max('id');
        if (!$lastID) {
            $lastID = 0;
        }
        if ($insert) {
            $this->po_code = Helper::makeCodeIncrement($lastID, '', '#PO');
        }
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    /**
     * @param $warehouse_id
     * @param $po_code
     * @param $sku
     * @param $qty
     * @param $type
     * @param null $order_code
     * @throws BadRequestHttpException
     */
    public static function checkStorage($order)
    {
        $skuItems = $order->skuItems;
        if (empty($skuItems)) {
            throw new BadRequestHttpException('Đơn hàng chưa có sản phẩm!');
        }

        foreach ($skuItems as $item) {
            $warehouse = WarehouseTransaction::findAll(['warehouse_id' => $order->warehouse_id, 'product_sku' => $item->sku]);
            if (!$warehouse) {
                throw new BadRequestHttpException("Không tồn tại sản phẩm <b class='text-warning'>{$item->sku}</b> trong kho!");
            }
        }
        return true;
    }
}
// Tạo đơn hàng -> chưa xuất hàng
// Chưa chuyển hàng -> chờ chuyển hàng
// Chờ chuyển hàng -> Đang vận chuyển
// Đang vận chuyển -> hoàn
