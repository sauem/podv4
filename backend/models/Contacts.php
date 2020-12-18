<?php

namespace backend\models;

use common\helper\Helper;
use phpDocumentor\Reflection\Types\Self_;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\BadRequestHttpException;

/**
 * This is the model class for table "contacts".
 *
 * @property int $id
 * @property int $register_time
 * @property string $code
 * @property string $phone
 * @property string|null $name
 * @property string|null $email
 * @property string|null $address
 * @property string|null $zipcode
 * @property string|null $option
 * @property string|null $ip
 * @property string|null $note
 * @property string|null $partner
 * @property string|null $hash_key
 * @property string|null $status
 * @property string|null $country
 * @property string|null $utm_source
 * @property string|null $utm_medium
 * @property string|null $utm_content
 * @property string|null $utm_term
 * @property string|null $utm_campaign
 * @property string|null $link
 * @property string|null $short_link
 * @property int $created_at
 * @property int $updated_at
 */
class Contacts extends \common\models\BaseModel
{
    /**
     * {@inheritdoc}
     */
    const STATUS_NEW = 'new';
    const STATUS_PENDING = 'pending';
    const STATUS_CALLBACK = 'callback';
    const STATUS_CANCEL = 'cancel';
    const STATUS_OK = 'ok';
    const STATUS_DUPLICATE = 'duplicate';
    const STATUS_NUMBER_FAIL = 'number_fail';

    const STATUS = [
        self::STATUS_NEW => 'Liên hệ mới',
        self::STATUS_PENDING => 'Thuê bao',
        self::STATUS_CALLBACK => 'Hẹn gọi lại',
        self::STATUS_CANCEL => 'Hủy đặt hàng',
        self::STATUS_OK => 'OK',
        self::STATUS_DUPLICATE => 'Trùng số',
        self::STATUS_NUMBER_FAIL => 'Sai số'
    ];

    public static function tableName()
    {
        return 'contacts';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['register_time', 'phone'], 'required'],
            [['register_time', 'created_at', 'updated_at'], 'integer'],
            [['option', 'type'], 'string'],
            [['code', 'name', 'email', 'address', 'zipcode', 'ip', 'note', 'partner', 'hash_key', 'country', 'utm_source', 'utm_medium', 'utm_content', 'utm_term', 'utm_campaign', 'link', 'short_link'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 25],
            [['status'], 'string', 'max' => 50],
            [['hash_key'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'register_time' => 'Ngày đặt',
            'code' => 'Code',
            'phone' => 'SĐT',
            'name' => 'Tên khách hàng',
            'email' => 'Email',
            'address' => 'Địa chỉ',
            'zipcode' => 'Zipcode',
            'option' => 'Yêu cầu',
            'ip' => 'Ip',
            'note' => 'Ghi chú',
            'partner' => 'Đối tác',
            'hash_key' => 'Hash Key',
            'status' => 'Trạng thái',
            'country' => 'Quốc gia',
            'utm_source' => 'Utm Source',
            'utm_medium' => 'Utm Medium',
            'utm_content' => 'Utm Content',
            'utm_term' => 'Utm Term',
            'utm_campaign' => 'Utm Campaign',
            'link' => 'Link',
            'short_link' => 'Short Link',
            'type' => 'Phân loại',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->hash_key = self::generateKey($this->phone, $this->partner, $this->option);
            $this->code = $this->code ? $this->code : self::generateCode($this->partner);
            if (self::isExisted($this->hash_key)) {
                $this->addError('register_time', 'Đã tồn tại liên hệ này trong 3 ngày trước!');
                return false;
            }
            if (!$this->status) {
                $this->status = self::STATUS_NEW;
            }
            if (!$this->country) {
                $this->country = static::getCountryPartner($this->partner);
            }
        }
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    static function getCountryPartner($partner)
    {
        $partner = UserModel::findOne(['username' => Helper::makeUpperString($partner)]);
        if (!$partner) {
            return null;
        }
        return $partner->country;
    }

    public static function isExisted($hash_key)
    {
        $model = Contacts::find()->where(['hash_key' => $hash_key])
            ->andWhere('FROM_UNIXTIME(register_time) >= NOW() - INTERVAL 3 DAY')->all();
        if ($model) {
            return true;
        }
        return false;
    }

    /**
     * @param $partnerName
     * @return string|string[]
     * @throws BadRequestHttpException
     */
    public static function generateCode($partnerName)
    {
        try {
            $maxId = Contacts::find()->max('id');
            if (!$maxId) {
                $maxId = 0;
            }
            $partner = UserModel::findOne(['username' => Helper::makeUpperString($partnerName)]);
            if (!$partner) {
                throw new BadRequestHttpException('Không tìm thấy đối tác!');
            }
        } catch (\Exception $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }
        return Helper::makeCodeIncrement($maxId, $partner->country);

    }

    /**
     * @param $code
     * @param $status
     * @throws BadRequestHttpException
     */
    public static function updateStatus($code, $order)
    {
        try {
            $model = Contacts::findOne(['code' => $code]);
            if (!$model) {
                throw new BadRequestHttpException('Không tồn tại đơn hàng này!');
            }
            $model->name = $order->name;
            $model->phone = $order->phone;
            $model->email = $order->email;
            $model->zipcode = $order->email;
            $model->address = $order->address;
            $model->country = $order->country;
            $model->status = self::STATUS_OK;
            return $model->save();
        } catch (\Exception $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }
    }

    public static function generateKey($phone, $partner, $option)
    {
        return md5($phone . $partner . $option);
    }

    public function getSale()
    {
        return $this->hasOne(ContactsAssignment::className(), ['phone' => 'phone'])
            ->with('user');
    }

    public static function StatusLabel($status)
    {
        switch ($status) {
            case self::STATUS_NEW:
                $color = 'info';
                break;
            case self::STATUS_CANCEL:
            case self::STATUS_DUPLICATE:
            case self::STATUS_NUMBER_FAIL:
                $color = 'secondary';
                break;
            case self::STATUS_PENDING:
            case self::STATUS_CALLBACK:
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
