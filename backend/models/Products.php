<?php

namespace backend\models;

use common\helper\Helper;
use Yii;

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
 */
class Products extends \common\models\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public $prices;
    public $thumb;
    public $avatar;

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
            [['category_id', 'partner_id', 'weight', 'created_at', 'updated_at'], 'integer'],
            [['sku', 'size'], 'string', 'max' => 255],
            [['sku'], 'unique'],
            [['prices', 'thumb', 'avatar'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getPrices()
    {
        return $this->hasMany(ProductsPrice::className(), ['sku' => 'sku']);
    }

    public function getCategory()
    {
        return $this->hasOne(Categories::className(), ['id' => 'category_id']);
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sku' => 'Tên sản phẩm',
            'category_id' => 'Danh mục',
            'partner_id' => 'Đối tác',
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
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public function afterFind()
    {
        $this->generateName();
        $this->avatar = $this->media ? $this->media->media->url : Helper::defaultImage('product');
        $this->thumb = $this->media ? $this->media->media->id : null;
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
        $this->sku = $category->name . '-' . $this->sku;
    }
}