<?php

namespace backend\models;

use common\helper\Helper;
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
 * @property string|null $vendor_status
 * @property string|null $cross_status
 * @property string|null $payment_status
 * @property string|null $checking_number
 * @property string|null $po_code
 * @property int|null $warehouse_id
 * @property int|null $transport_id
 * @property int $created_at
 * @property init|null $order_time
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
    public $po_code;
    public $payment_status;

    const STATUS_NEW = 'new';
    const STATUS_PENDING = 'pending';
    const STATUS_SHIPPING = 'shipping';
    const STATUS_SHIPPED = 'shipped';


    const STATUS_PAYED = 'paid';
    const STATUS_UNPAID = 'unpaid';
    const STATUS_REFUND = 'refund';

    const STATUS_CANCEL = 'cancel';
    const STATUS_UNCROSS = 'uncross';
    const STATUS_CROSSED = 'crossed';

    const STATUS = [
        self::STATUS_NEW => 'Chưa vận chuyển',
        self::STATUS_PENDING => 'Chờ vận chuyển',
        self::STATUS_SHIPPING => 'Đang vận chuyển',
        self::STATUS_SHIPPED => 'Đã vận chuyển',
        self::STATUS_PAYED => 'Chưa thanh toán',
        self::STATUS_UNPAID => 'Đã thanh toán',
        self::STATUS_REFUND => 'Hoàn đơn',
        self::STATUS_UNCROSS => 'Chưa đối soát',
        self::STATUS_CROSSED => 'Đã đối soát',
        self::STATUS_CANCEL => 'Huỷ đơn',

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
            [['total_bill', 'total_price'], 'number'],
            [['payment_method', 'created_at', 'updated_at', 'warehouse_id', 'transport_id',
                'transport_id', 'warehouse_id', 'order_time'], 'integer'],
            [['name', 'code', 'address', 'zipcode', 'email', 'city',
                'checking_number', 'vendor_status', 'cross_status',
                'district', 'order_source', 'note', 'vendor_note', 'country', 'checking_number', 'po_code'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 25],
            [['shipping_cost'], 'safe'],
            [['status', 'vendor_status', 'cross_status', 'payment_status'], 'string', 'max' => 100],
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
            'checking_number' => 'Mã vận chuyển',
            'warehouse_id' => 'Kho',
            'transport_id' => 'Đơn vị vận chuyển',
            'po_code' => 'Mã nhập hàng',
            'payment_status' => 'Trạng thái thanh toán',
            'cross_status' => 'Trạng thái đối xoát',
            'order_time' => 'Ngày lên đơn',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[OrdersContactSkus]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSkuItems()
    {
        return $this->hasMany(OrdersContactSku::className(), ['order_id' => 'id']);
    }

    public function getContact()
    {
        return $this->hasOne(Contacts::className(), ['code' => 'code']);
    }

    public function getWarehouse()
    {
        return $this->hasOne(Warehouse::className(), ['id' => 'warehouse_id']);
    }

    public function getPayment()
    {
        return $this->hasOne(Payments::className(), ['id' => 'payment_method']);
    }

    public function getTransporter()
    {
        return $this->hasOne(Transporters::className(), ['id' => 'transport_id']);
    }

    public function beforeSave($insert)
    {
        if ($this->shipping_cost) {
            $this->shipping_cost = Helper::toFloat($this->shipping_cost);
        }
        if ($this->checking_number) {
            $this->status = self::STATUS_SHIPPING;
        }
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
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
