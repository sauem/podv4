<?php

namespace backend\models;

use common\helper\Helper;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "products".
 *
 * @property int $id
 * @property string|null $sku
 * @property int $category_id
 * @property int $partner_id
 * @property string|null $size
 * @property int|null $weight
 * @property int $created_at
 * @property int $updated_at
 * @property string|null $partner_name
 * @property int|null $marketer_id
 * @property int|null $marketer_rage_start
 * @property int|null $marketer_rage_end
 * @property string|null $marketer_time;
 */
class Products extends \common\models\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public $prices;
    public $thumb;
    public $avatar;
    public $name;
    public $marketer_time;

    public static function tableName()
    {
        return 'products';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sku', 'category_id'], 'required'],
            [['category_id', 'partner_id', 'weight', 'created_at', 'updated_at', 'marketer_rage_start',
                'marketer_rage_end', 'marketer_id'], 'integer'],
            [['sku', 'size', 'name', 'partner_name'], 'string', 'max' => 255],
            [['sku'], 'unique'],
            [['prices', 'thumb', 'avatar', 'marketer_time'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getItemPrices()
    {
        return $this->hasMany(ProductsPrice::className(), ['sku' => 'sku']);
    }

    public function getCategory()
    {
        return $this->hasOne(Categories::className(), ['id' => 'category_id']);
    }

    public function getPartner()
    {
        return $this->hasOne(UserModel::className(), ['id' => 'partner_id']);
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sku' => 'Mã sản phẩm',
            'category_id' => 'Loại sản phẩm',
            'partner_id' => 'Đối tác',
            'partner_name' => 'Tên đối tác',
            'name' => 'Tên sản phẩm',
            'size' => 'Kích thước',
            'weight' => 'Cân nặng',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->sku = strtoupper(Helper::toLower($this->sku));
            $this->partner_id = $this->category->partner_id;
        }
        $pn = UserModel::findOne($this->partner_id);
        $this->partner_name = $pn ? $pn->username : null;
        if ($this->marketer_time && !empty($this->marketer_time)) {
            $time = explode(" - ", $this->marketer_time);
            $startAt = Helper::timer(str_replace("/", "-", $time[0]));
            $endAt = Helper::timer(str_replace("/", "-", $time[1]));
            $this->marketer_rage_start = $startAt;
            $this->marketer_rage_end = $endAt;
        }
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public function afterFind()
    {
        $this->generateName();
        $this->avatar = $this->media ? $this->media->media->url : Helper::defaultImage('product');
        $this->thumb = $this->media ? $this->media->media->id : null;
        if ($this->marketer_rage_start && $this->marketer_rage_end) {
            $this->marketer_time = date('d/m/Y', $this->marketer_rage_start) . ' - ' . date('d/m/Y', $this->marketer_rage_end);
        }
        parent::afterFind(); // TODO: Change the autogenerated stub
    }

    public function getMedia()
    {
        return $this->hasOne(MediaObj::className(),
            ['obj_id' => 'id'])
            ->where(['{{media_obj}}.obj_type' => MediaObj::OBJECT_PRODUCT])
            ->with('media');
    }

    private function generateName()
    {
        $category = Categories::findOne($this->category_id);
        if (!$category) {
            $this->addError('partner_id', 'Không tìm thấy danh mục!');
            return false;
        }
        $this->name = $category->name . '-' . $this->sku;
    }

    public static function LISTS()
    {
        $all = Products::find()->all();
        return ArrayHelper::map($all, 'sku', 'sku');
    }
}
