<?php

namespace backend\models;

use Yii;
use yii\web\BadRequestHttpException;
use common\helper\Helper;

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
            [['qty', 'price', 'sku'], 'required'],
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

    /**
     * @param $sku
     * @param array $prices
     * @return bool
     * @throws BadRequestHttpException
     */
    public static function savePrice($sku, $prices = [])
    {
        if (!empty($prices)) {
            foreach ($prices as $price) {
                $model = new ProductsPrice();
                $model->sku = $sku;
                $model->price = Helper::toFloat($price['price']);
                $model->qty = $price['qty'];
                if (!$model->save()) {
                    throw new BadRequestHttpException(Helper::firstError($model));
                }
            }
            return true;
        }
    }

    /**
     * @param $sku
     * @throws BadRequestHttpException
     */
    public static function removePrice($sku)
    {
        try {
            ProductsPrice::deleteAll(['sku' => $sku]);
        } catch (\Exception $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }
    }
}
