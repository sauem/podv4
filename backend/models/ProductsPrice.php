<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "products_price".
 *
 * @property int $id
 * @property string|null $sku
 * @property int|null $qty
 * @property float|null $price
 * @property int $created_at
 * @property int $updated_at
 */
class ProductsPrice extends \common\models\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'products_price';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['qty', 'created_at', 'updated_at'], 'integer'],
            [['price'], 'number'],
            [['created_at', 'updated_at'], 'required'],
            [['sku'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sku' => 'Mã sản phẩm',
            'qty' => 'Số lượng',
            'price' => 'Giá',
            'created_at' => 'Ngày tạo',
            'updated_at' => 'Ngày cập nhật',
        ];
    }

    public static function addMultiplePrice()
    {

    }
}
