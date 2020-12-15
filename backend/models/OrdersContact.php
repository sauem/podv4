<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\BadRequestHttpException;

/**
 * This is the model class for table "orders_contact".
 *
 * @property int $id
 * @property string $name
 * @property string $phone
 * @property string $country
 * @property string|null $code
 * @property string $address
 * @property string $zipcode
 * @property float|null $shipping_cost
 * @property float|null $total_bill
 * @property float|null $total_price
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
    public $country;
    public $total_bill;
    public $total_price;

    const STATUS_NEW = 'new';
    const STATUS_PENDING = 'pending';
    const STATUS_SHIPPING = 'shipping';
    const STATUS_SHIPPED = 'shipped';

    const STATUS = [
        self::STATUS_NEW => 'Chưa vận chuyển',
        self::STATUS_PENDING => 'Chờ vận chuyển',
        self::STATUS_SHIPPING => 'Đang vận chuyển',
        self::STATUS_SHIPPED => 'Đã vận chuyển'
    ];

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
            [['name', 'phone', 'address', 'zipcode', 'payment_method', 'zipcode', 'code'], 'required'],
            [['shipping_cost', 'total_bill', 'total_price'], 'number'],
            [['payment_method', 'created_at', 'updated_at'], 'integer'],
            [['name', 'code', 'address', 'zipcode', 'email', 'city', 'district', 'order_source', 'note', 'vendor_note', 'country'], 'string', 'max' => 255],
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
            'name' => 'Khách hàng',
            'phone' => 'Số điện thoại',
            'code' => 'Mã đơn',
            'address' => 'Địa chỉ',
            'zipcode' => 'Mã bưu chính',
            'shipping_cost' => 'Phí giao hàng',
            'payment_method' => 'Hình thức thanh toán',
            'email' => 'Email',
            'city' => 'Tỉnh/Thành phố',
            'country' => 'Quốc gia',
            'district' => 'Quận/Huyện',
            'order_source' => 'Nguồn liên hệ',
            'note' => 'Ghi chú sale',
            'vendor_note' => 'Ghi chú cho đơn vị vận chuyển',
            'status' => 'Trạng thái',
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

    public function getContact()
    {
        return $this->hasOne(Contacts::className(), ['code' => 'code']);
    }

    public static function StatusLabel($status)
    {
        switch ($status) {
            case self::STATUS_NEW:
                $color = 'primary';
                break;
            case self::STATUS_PENDING:
                $color = 'info';
                break;
            case self::STATUS_SHIPPING:
                $color = 'warning';
                break;
            default:
                $color = 'success';
        }
        return Html::tag('span', ArrayHelper::getValue(self::STATUS, $status, '--'), [
            'class' => "badge badge-pill m-auto badge-$color"
        ]);
    }

}
