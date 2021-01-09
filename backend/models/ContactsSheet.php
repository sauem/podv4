<?php

namespace backend\models;

use phpDocumentor\Reflection\Types\Self_;
use Yii;

/**
 * This is the model class for table "contacts_sheet".
 *
 * @property int $id
 * @property int|null $partner_id
 * @property int|null $category_id
 * @property string|null $sheet_id
 * @property string|null $country
 * @property string|null $contact_source
 * @property int $created_at
 * @property int $updated_at
 */
class ContactsSheet extends \common\models\BaseModel
{
    /**
     * {@inheritdoc}
     */
    const SOURCE_REQUIRED = 'required';
    const SOURCE_UN_REQUIRED = 'un_required';
    const SOURCE_REQUIRE = [
        self::SOURCE_REQUIRED => 'Bắt buộc',
        self::SOURCE_UN_REQUIRED => 'Không bắt buộc'
    ];

    public static function tableName()
    {
        return 'contacts_sheet';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['partner_id', 'category_id', 'created_at', 'updated_at'], 'integer'],
            [['sheet_id'], 'string'],
            [['sheet_id', 'category_id', 'country', 'partner_id'], 'required'],
            [['country', 'contact_source'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'partner_id' => 'Đối tác',
            'category_id' => 'Loại sản phẩm',
            'sheet_id' => 'Sheet ID',
            'country' => 'Thị trường',
            'contact_source' => 'Nguồn contact bắt buộc',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getPartner()
    {
        return $this->hasOne(UserModel::className(), ['id' => 'partner_id']);
    }

    public function getCategory()
    {
        return $this->hasOne(Categories::className(), ['id' => 'category_id']);
    }

}
