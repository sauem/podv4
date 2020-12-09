<?php

namespace backend\models;

use Yii;

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
            [['register_time', 'code', 'phone', 'created_at', 'updated_at'], 'required'],
            [['register_time', 'created_at', 'updated_at'], 'integer'],
            [['option'], 'string'],
            [['code', 'name', 'email', 'address', 'zipcode', 'ip', 'note', 'partner', 'hash_key', 'country', 'utm_source', 'utm_medium', 'utm_content', 'utm_term', 'utm_campaign', 'link', 'short_link'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 25],
            [['status'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'register_time' => 'Register Time',
            'code' => 'Code',
            'phone' => 'Phone',
            'name' => 'Name',
            'email' => 'Email',
            'address' => 'Address',
            'zipcode' => 'Zipcode',
            'option' => 'Option',
            'ip' => 'Ip',
            'note' => 'Note',
            'partner' => 'Partner',
            'hash_key' => 'Hash Key',
            'status' => 'Status',
            'country' => 'Country',
            'utm_source' => 'Utm Source',
            'utm_medium' => 'Utm Medium',
            'utm_content' => 'Utm Content',
            'utm_term' => 'Utm Term',
            'utm_campaign' => 'Utm Campaign',
            'link' => 'Link',
            'short_link' => 'Short Link',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
