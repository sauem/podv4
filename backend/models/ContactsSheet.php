<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "contacts_sheet".
 *
 * @property int $id
 * @property int|null $partner_id
 * @property string|null $sku
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
            [['partner_id', 'created_at', 'updated_at'], 'integer'],
            [['sheet_id'], 'string'],
            [['sku', 'partner_id', 'sheet_id', 'country'], 'required'],
            [['sku', 'country', 'contact_source'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getPartner()
    {
        return $this->hasOne(UserModel::className(), ['id' => 'partner_id']);
    }

    public function getSource()
    {
        return $this->hasOne(ContactsSource::className(), ['slug' => 'contact_source']);
    }

    public function getProduct()
    {
        return $this->hasOne(Products::className(), ['sku' => 'sku']);
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'partner_id' => 'Mã đối tác',
            'sku' => 'Sản phẩm',
            'sheet_id' => 'Sheet ID',
            'country' => 'Thị trường',
            'contact_source' => 'Nguồn liên hệ',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
