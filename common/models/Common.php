<?php


namespace common\models;


use yii\base\Model;
use yii\db\ActiveRecord;

class Common extends Model
{
    public $sheet_id;
    public $phone_prioritize_time;
    public $callback_to_cancel_time;
    public $description;
    public $sitename;
    public $notify;

    public function rules()
    {
        return [
            [['callback_to_cancel_time'], 'number'],
            [['phone_prioritize_time', 'sheet_id', 'sitename', 'description', 'notify'], 'string']
        ];
    }

    public function attributeLabels()
    {
        return [
            'sitename' => 'Tên hệ thống',
            'description' => 'Mô tả hệ thống',
            'notify' => 'Thông báo chung',
            'sheet_id' => 'ID google sheet backup contact',
            'phone_prioritize_time' => 'Thời gian ưu tiên số mới (ngày)',
            'callback_to_cancel_time' => 'Thời gian hủy số gọi lại (ngày)',
        ];
    }
}
