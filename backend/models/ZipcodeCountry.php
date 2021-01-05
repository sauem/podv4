<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "zipcode_country".
 *
 * @property int $id
 * @property string $name
 * @property string|null $code
 * @property string $zipcode
 * @property string|null $city
 * @property string|null $district
 * @property string|null $address
 * @property int $created_at
 * @property int $updated_at
 * @property string|null $symbol
 */
class ZipcodeCountry extends \common\models\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'zipcode_country';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['zipcode', 'code'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['name', 'code', 'zipcode', 'city', 'district', 'address'], 'string', 'max' => 255],
            [['symbol'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Tên quốc gia',
            'code' => 'Mã vùng',
            'zipcode' => 'Mã bưu điện',
            'city' => 'Tỉnh/Thành phố',
            'district' => 'Quận/Huyện',
            'address' => 'Địa chỉ',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'symbol' => 'Đơn vị tiền tệ',
        ];
    }
}
