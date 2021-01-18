<?php

namespace backend\models;

use common\helper\Helper;
use common\models\User;
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
 * @property string|null $shipping_status
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
 * @property int|null $time_paid_success
 * @property string|null $bill_link
 * @property string|null $partner_name
 * @property float|null $service_fee
 * @property int|null $sale
 * @property int|null $partner
 *
 * @property OrdersContactSku[] $ordersContactSkus
 */
class OrdersContact extends \common\models\BaseModel
{
    public $partner;
    public $partner_name;
    public $items;

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
            [['name', 'phone', 'address', 'zipcode', 'country', 'payment_method', 'shipping_cost'], 'required'],
            [['total_bill', 'total_price', 'cod_cost', 'collection_fee', 'transport_fee', 'service_fee'], 'number'],
            [['payment_method', 'created_at', 'updated_at', 'warehouse_id', 'transport_id', 'order_time', 'sub_transport_id', 'time_shipped_success', 'remittance_date', 'time_paid_success', 'sale'], 'integer'],
            [['bill_link'], 'string'],
            [['name', 'partner_name', 'code', 'address', 'zipcode', 'email', 'city', 'district', 'order_source', 'note', 'vendor_note', 'country', 'checking_number', 'po_code', 'sub_transport_tracking', 'cross_check_code'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 25],
            //[['code'], 'unique'],
            [['status'], 'string', 'max' => 100],
            [['partner', 'items', 'shipping_cost'], 'safe'],
            [['payment_status', 'cross_status', 'shipping_status'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'customer'),
            'phone' => Yii::t('app', 'phone'),
            'code' => Yii::t('app', 'order_code'),
            'address' => Yii::t('app', 'address'),
            'zipcode' => Yii::t('app', 'zipcode'),
            'shipping_cost' => Yii::t('app', 'shipping_fee'),
            'payment_method' => Yii::t('app', 'payment_method'),
            'email' => Yii::t('app', 'Email'),
            'city' => Yii::t('app', 'province'),
            'district' => Yii::t('app', 'district'),
            'order_source' => Yii::t('app', 'contact_source'),
            'note' => Yii::t('app', 'note'),
            'vendor_note' => Yii::t('app', 'delivery_note'),
            'status' => Yii::t('app', 'status'),
            'created_at' => Yii::t('app', 'created_at'),
            'updated_at' => Yii::t('app', 'updated_at'),
            'country' => Yii::t('app', 'country'),
            'total_bill' => Yii::t('app', 'total_amount'),
            'total_price' => Yii::t('app', 'amount'),
            'warehouse_id' => Yii::t('app', 'warehouse'),
            'transport_id' => Yii::t('app', 'delivery_partner'),
            'checking_number' => Yii::t('app', 'tracking_number'),
            'payment_status' => Yii::t('app', 'payment_status'),
            'cross_status' => Yii::t('app', 'cross_status'),
            'shipping_status' => Yii::t('app', 'shipping_status'),
            'po_code' => Yii::t('app', 'po_code'),
            'order_time' => Yii::t('app', 'order_time'),
            'sub_transport_id' => Yii::t('app', 'sub_transport'),
            'sub_transport_tracking' => Yii::t('app', 'sub_transport_code'),
            'cod_cost' => Yii::t('app', 'cod_cost'),
            'time_shipped_success' => Yii::t('app', 'time_shipped_success'),
            'collection_fee' => Yii::t('app', 'collection_fee'),
            'transport_fee' => Yii::t('app', 'transport_fee'),
            'remittance_date' => Yii::t('app', 'cross_check_time'),
            'cross_check_code' => Yii::t('app', 'cross_check_code'),
            'time_paid_success' => Yii::t('app', 'time_paid_success'),
            'bill_link' => Yii::t('app', 'bill_link'),
            'service_fee' => Yii::t('app', 'service_fee'),
            'sale' => Yii::t('app', 'sale'),
        ];
    }

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
        self::STATUS_UNPAID => 'Chưa thanh toán',
        self::STATUS_PAYED => 'Đã thanh toán',
        self::STATUS_REFUND => 'Hoàn đơn',
        self::STATUS_UNCROSS => 'Chưa đối soát',
        self::STATUS_CROSSED => 'Đã đối soát',
        self::STATUS_CANCEL => 'Huỷ đơn',

    ];
    const STATIC_PAYMENT_TRANSFER = 9999;
    const STATIC_PAYMENT = [
        self::STATIC_PAYMENT_TRANSFER => 'Chuyển khoản'
    ];

    public static function find()
    {
        return parent::find()->where(['{{orders_contact}}.country' => Yii::$app->cache->get('country')]);
    }

    public function getSkuItems()
    {
        return $this->hasMany(OrdersContactSku::className(), ['order_id' => 'id'])->with('product');
    }

    public function getContact()
    {
        return $this->hasOne(Contacts::className(), ['code' => 'code'])->with('partner');
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

    public function beforeValidate()
    {

        if (!Helper::isEmpty($this->partner_name)) {
            $partner = UserModel::findOne(['username' => $this->partner_name]);
            if ($partner) {
                $this->country = $partner->country;
            }
        }
        if (!Helper::isEmpty($this->payment_method) && is_string($this->payment_method)) {
            $payment = Payments::findOne(['slug' => Helper::toLower($this->payment_method)]);
            if ($payment) {
                $this->payment_method = $payment->id;
            } else {
                $this->payment_method = OrdersContact::STATIC_PAYMENT_TRANSFER;
            }
        }
        if (!Helper::isEmpty($this->sale) && is_string($this->sale)) {
            $sale = UserModel::findOne(['username' => $this->sale]);
            if ($sale) {
                $this->sale = $sale->id;
            }
        } else {
            $this->sale = Yii::$app->user->getId();
        }
        return parent::beforeValidate(); // TODO: Change the autogenerated stub
    }

    public function beforeSave($insert)
    {
        if (!empty($this->items)) {
            $item = array_values($this->items);
            $sku = ArrayHelper::getValue(array_shift($item), 'sku', null);
            $product = Products::findOne(['sku' => $sku]);
            if (!$product) {
                throw new BadRequestHttpException("Không tìm thấy sản phẩm!");
            }
            $partner = UserModel::findOne($product->partner_id);
            if (!$partner) {
                throw new BadRequestHttpException("Không tìm thấy đối tác!");
            }
            $this->service_fee = $partner->service_fee;
            $this->country = $partner->country;

            if (Helper::isEmpty($this->code)) {
                try {
                    $this->code = Contacts::generateCode($partner->username, $this->country);
                } catch (\Exception $e) {
                    $this->addError('code', $e->getMessage() . $this->code);
                    return false;
                }
            }
        }

        if (!Helper::isEmpty($this->status)) {
            $this->status = Helper::toLower($this->status);
        }
        if (!Helper::isEmpty($this->shipping_cost)) {
            $this->shipping_cost = Helper::toFloat($this->shipping_cost);
        }
        if (!Helper::isEmpty($this->total_bill)) {
            $this->total_bill = Helper::toFloat($this->total_bill);
        }
        if (!Helper::isEmpty($this->total_price)) {
            $this->total_price = Helper::toFloat($this->total_price);
        }
        if ($insert) {
            if ($this->payment_method === self::STATIC_PAYMENT_TRANSFER && Helper::isEmpty($this->bill_link)) {
                $this->addError("bill_link", "Link hóa đơn chuyển khoản trống!");
                return false;
            }
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
