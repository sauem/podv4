<?php

namespace backend\models;

use common\helper\Helper;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * This is the model class for table "orders_contact".
 *
 * @property int $id
 * @property string $name
 * @property string $phone
 * @property string|null $code
 * @property string $address
 * @property string $zipcode
 * @property float|null $shipping_cost
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
 * @property string|null $country
 * @property float|null $total_bill
 * @property float|null $total_price
 * @property int|null $warehouse_id
 * @property int|null $transport_id
 * @property string|null $checking_number
 * @property string|null $payment_status
 * @property string|null $cross_status
 * @property string|null $vendor_status
 * @property string|null $po_code
 * @property int|null $order_time
 * @property int|null $sub_transport_id
 * @property string|null $sub_transport_tracking
 * @property float|null $cod_cost
 * @property int|null $time_shipped_success
 * @property float|null $collection_fee
 * @property float|null $transport_fee
 * @property int|null $remittance_date
 * @property string|null $cross_check_code
 *
 * @property OrdersContactSku[] $ordersContactSkus
 */
class OrdersContact extends \common\models\BaseModel
{


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


    /**
     * {@inheritdoc}
     */
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
            [['name', 'phone', 'address', 'zipcode', 'created_at', 'updated_at'], 'required'],
            [['shipping_cost', 'total_bill', 'total_price', 'cod_cost', 'collection_fee', 'transport_fee'], 'number'],
            [['payment_method', 'created_at', 'updated_at', 'warehouse_id', 'transport_id', 'order_time', 'sub_transport_id', 'time_shipped_success', 'remittance_date'], 'integer'],
            [['name', 'code', 'address', 'zipcode', 'email', 'city', 'district', 'order_source', 'note', 'vendor_note', 'country', 'checking_number', 'po_code', 'sub_transport_tracking', 'cross_check_code'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 25],
            [['status'], 'string', 'max' => 100],
            [['payment_status', 'cross_status', 'vendor_status'], 'string', 'max' => 50],
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
            'code' => 'Mã đơn hàng',
            'address' => 'Địa chỉ',
            'zipcode' => 'Mã bưu chính',
            'shipping_cost' => 'Phi giao hàng',
            'payment_method' => 'Hình thức thanh toán',
            'email' => 'Email',
            'city' => 'Tỉnh/Thành phố',
            'district' => 'Quận/Huyện',
            'order_source' => 'Nguồn liên hệ',
            'note' => 'Ghi chú đơn hàng',
            'vendor_note' => 'Ghi chú cho đơn vị vận chuyển',
            'status' => 'Trạng thái đơn hàng',
            'created_at' => 'Ngày tạo đơn',
            'updated_at' => 'Updated At',
            'country' => 'Quốc gia',
            'total_bill' => 'Thành tiền',
            'total_price' => 'Tổng tiền',
            'warehouse_id' => 'Kho',
            'transport_id' => 'Đối tác vận chuyển',
            'checking_number' => 'Mã vận chuyển đối tác',
            'payment_status' => 'Trạng thái thanh toán',
            'cross_status' => 'Trạng thái đối soát',
            'vendor_status' => 'Trạng thái giao hàng',
            'po_code' => 'Mã nhập kho',
            'order_time' => 'Ngày đặt hàng',
            'sub_transport_id' => 'Đơn vị vận chuyển',
            'sub_transport_tracking' => 'Mã vận chuyển đơn vị vận chuyển',
            'cod_cost' => 'Tiền COD',
            'time_shipped_success' => 'Ngày chuyển hàng thành công',
            'collection_fee' => 'Phí thu hộ',
            'transport_fee' => 'Phí vận chuyển',
            'remittance_date' => 'Ngày chuyển tiền',
            'cross_check_code' => 'Mã biên bản đối soát',
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
